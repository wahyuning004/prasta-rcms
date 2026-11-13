<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Concerns\ManagesUserProfile;
use App\Http\Controllers\Controller;
use App\Models\FreeConsultation;
use App\Models\Order;
use App\Models\Promo;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MarketingController extends Controller
{
    use ManagesUserProfile;

    /**
     * Dashboard ringkasan / monitoring Marketing & Client Relations.
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        $promos = Promo::orderByDesc('created_at')->get();
        $stats = $this->buildOperationalStats($promos);

        return view('marketing.dashboard', compact('user', 'stats'));
    }

    /**
     * Halaman operasional Tiket, Konsultasi Gratis, Inquiry & Live Chat.
     */
    public function tickets(Request $request): View
    {
        $user = Auth::user();
        $promos = Promo::orderByDesc('created_at')->get();
        $stats = $this->buildOperationalStats($promos);

        $consultationsQuery = DB::table('free_consultations')
            ->leftJoin('users', 'free_consultations.user_id', '=', 'users.id')
            ->select(
                'free_consultations.*',
                DB::raw('COALESCE(users.name, free_consultations.name) as client_name'),
                'users.company_name',
                DB::raw('COALESCE(users.phone, free_consultations.phone) as phone'),
                DB::raw('COALESCE(users.email, free_consultations.email) as email')
            );

        $statusFilter = $request->get('status');
        if ($statusFilter && $statusFilter !== 'all') {
            $consultationsQuery->where('free_consultations.status', $statusFilter);
        }

        $sourceFilter = $request->get('source');
        if ($sourceFilter === 'inquiry') {
            $consultationsQuery->where(function ($q): void {
                $q->whereNull('free_consultations.user_id')
                    ->orWhere('free_consultations.subject', 'like', 'Konsultasi Layanan:%');
            });
        } elseif ($sourceFilter === 'consultation') {
            $consultationsQuery->whereNotNull('free_consultations.user_id')
                ->where('free_consultations.subject', 'not like', 'Konsultasi Layanan:%');
        }

        $consultations = $consultationsQuery->latest('free_consultations.created_at')->get();

        return view('marketing.tickets', compact(
            'user',
            'stats',
            'consultations',
            'statusFilter',
            'sourceFilter'
        ));
    }

    /**
     * Halaman operasional Kupon & Kuota Promo beserta Riwayat Penggunaan Kupon.
     */
    public function promos(Request $request): View
    {
        $user = Auth::user();
        $promos = Promo::orderByDesc('created_at')->get();
        $stats = $this->buildOperationalStats($promos);
        $usageData = $this->buildPromoUsageHistory($request, $promos);

        return view('marketing.promos', array_merge(
            compact('user', 'stats', 'promos'),
            $usageData
        ));
    }

    /**
     * Halaman operasional Moderasi Ulasan Klien.
     */
    public function reviews(Request $request): View
    {
        $user = Auth::user();
        $promos = Promo::orderByDesc('created_at')->get();
        $stats = $this->buildOperationalStats($promos);

        $reviewStatusFilter = $request->get('review_status');
        $reviewsQuery = Review::with(['user', 'service', 'order.service', 'order.items.service']);
        if ($reviewStatusFilter && $reviewStatusFilter !== 'all') {
            $normalizedReviewStatus = $reviewStatusFilter === 'approved' ? 'published' : $reviewStatusFilter;
            $reviewsQuery->where('status', $normalizedReviewStatus);
        }
        $reviews = $reviewsQuery->latest('created_at')->get();

        return view('marketing.reviews', compact(
            'user',
            'stats',
            'reviews',
            'reviewStatusFilter'
        ));
    }

    /**
     * Hitung metrik operasional Marketing: pertanyaan masuk, konversi calon pelanggan, & penggunaan promosi.
     *
     * @param  Collection<int, Promo>  $promos
     * @return array<string, mixed>
     */
    protected function buildOperationalStats(Collection $promos): array
    {
        // A. Pertanyaan Masuk (free_consultations: layanan/tamu vs akun klien terdaftar)
        $allConsultations = FreeConsultation::select(['id', 'user_id', 'email', 'phone', 'subject', 'status'])->get();
        $totalConsultations = $allConsultations->count();
        $pendingConsultations = $allConsultations->where('status', 'pending')->count();
        $respondedConsultations = $allConsultations->where('status', 'responded')->count();

        $serviceInquiryCount = $allConsultations->filter(function (FreeConsultation $item): bool {
            return $item->user_id === null || str_starts_with((string) $item->subject, 'Konsultasi Layanan:');
        })->count();
        $directConsultationCount = max(0, $totalConsultations - $serviceInquiryCount);

        // B. Konversi Calon Pelanggan -> Daftar Akun -> Menjadi Pelanggan/Klien (Memiliki Pesanan)
        $clientUsers = User::whereIn('role', ['klien', 'klient', 'client'])
            ->get(['id', 'email', 'phone', 'email_verified_at']);

        $registeredClientsCount = $clientUsers->count();
        $verifiedClientsCount = $clientUsers->whereNotNull('email_verified_at')->count();
        $unverifiedClientsCount = max(0, $registeredClientsCount - $verifiedClientsCount);

        $clientUserIds = $clientUsers->pluck('id')->all();
        $convertedCustomersCount = empty($clientUserIds)
            ? 0
            : (int) Order::whereIn('user_id', $clientUserIds)->distinct('user_id')->count('user_id');

        $guestLeadCount = $this->countUniqueGuestLeads($allConsultations, $clientUsers);
        $totalProspects = $registeredClientsCount + $guestLeadCount;

        $prospectToRegisteredRate = $totalProspects > 0
            ? round(($registeredClientsCount / $totalProspects) * 100, 1)
            : 0.0;
        $registeredToCustomerRate = $registeredClientsCount > 0
            ? round(($convertedCustomersCount / $registeredClientsCount) * 100, 1)
            : 0.0;
        $prospectToCustomerRate = $totalProspects > 0
            ? round(($convertedCustomersCount / $totalProspects) * 100, 1)
            : 0.0;

        // C. Penggunaan Promosi (Status akurat termasuk kedaluwarsa & kuota habis)
        $expiredPromos = $promos->filter(fn (Promo $promo): bool => $promo->isExpired())->count();
        $activePromos = $promos->filter(fn (Promo $promo): bool => $promo->is_active && ! $promo->isExpired())->count();
        $inactivePromos = $promos->filter(fn (Promo $promo): bool => ! $promo->is_active && ! $promo->isExpired())->count();
        $exhaustedPromos = $promos->filter(fn (Promo $promo): bool => (int) $promo->quota > 0 && (int) $promo->used_count >= (int) $promo->quota)->count();

        $totalPromosUsed = (int) $promos->sum('used_count');
        $usedVouchersCount = $promos->where('used_count', '>', 0)->count();
        $topPromo = $promos->where('used_count', '>', 0)->sortByDesc('used_count')->first();
        $ordersWithPromoCount = Order::where(function ($q): void {
            $q->whereNotNull('promo_id')->orWhereNotNull('promo_code');
        })->count();
        $monthlyPromoOrdersCount = Order::where(function ($q): void {
            $q->whereNotNull('promo_id')->orWhereNotNull('promo_code');
        })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // D. Ulasan & Rating
        $totalReviews = Review::count();
        $publishedReviews = Review::where('status', 'published')->count();
        $pendingReviews = Review::where('status', 'pending')->count();
        $rejectedReviews = Review::where('status', 'rejected')->count();
        $avgRating = $totalReviews > 0 ? round((float) Review::avg('rating'), 1) : 0.0;

        return [
            'pendingConsultations' => $pendingConsultations,
            'respondedConsultations' => $respondedConsultations,
            'totalConsultations' => $totalConsultations,
            'serviceInquiryCount' => $serviceInquiryCount,
            'directConsultationCount' => $directConsultationCount,
            'guestLeadCount' => $guestLeadCount,
            'totalProspects' => $totalProspects,
            'registeredClientsCount' => $registeredClientsCount,
            'verifiedClientsCount' => $verifiedClientsCount,
            'unverifiedClientsCount' => $unverifiedClientsCount,
            'convertedCustomersCount' => $convertedCustomersCount,
            'prospectToRegisteredRate' => $prospectToRegisteredRate,
            'registeredToCustomerRate' => $registeredToCustomerRate,
            'prospectToCustomerRate' => $prospectToCustomerRate,
            'totalPromos' => $promos->count(),
            'activePromos' => $activePromos,
            'expiredPromos' => $expiredPromos,
            'inactivePromos' => $inactivePromos,
            'exhaustedPromos' => $exhaustedPromos,
            'totalPromosUsed' => $totalPromosUsed,
            'usedVouchersCount' => $usedVouchersCount,
            'topPromoCode' => $topPromo?->code,
            'topPromoUsedCount' => (int) ($topPromo?->used_count ?? 0),
            'ordersWithPromoCount' => $ordersWithPromoCount,
            'monthlyPromoOrdersCount' => $monthlyPromoOrdersCount,
            'totalReviews' => $totalReviews,
            'publishedReviews' => $publishedReviews,
            'pendingReviews' => $pendingReviews,
            'rejectedReviews' => $rejectedReviews,
            'avgRating' => $avgRating,
        ];
    }

    /**
     * Hitung calon pelanggan unik (tamu konsultasi & klaim promo) yang belum terdaftar sebagai akun klien.
     *
     * @param  Collection<int, FreeConsultation>  $consultations
     * @param  Collection<int, User>  $clientUsers
     */
    protected function countUniqueGuestLeads(Collection $consultations, Collection $clientUsers): int
    {
        $knownEmails = $clientUsers->pluck('email')
            ->filter()
            ->map(fn (string $e): string => strtolower(trim($e)))
            ->flip()
            ->all();

        $knownPhones = $clientUsers->pluck('phone')
            ->filter()
            ->map(fn (string $p): string => $this->normalizePhoneKey($p))
            ->filter()
            ->flip()
            ->all();

        $guestKeys = [];

        foreach ($consultations as $item) {
            if ($item->user_id !== null) {
                continue;
            }
            $key = $this->extractGuestLeadKey($item->email, $item->phone, $knownEmails, $knownPhones, 'consult_'.$item->id);
            if ($key !== null) {
                $guestKeys[$key] = true;
            }
        }

        if (Schema::hasTable('promo_claims')) {
            $claims = DB::table('promo_claims')->select(['id', 'email', 'phone'])->get();
            foreach ($claims as $claim) {
                $key = $this->extractGuestLeadKey($claim->email, $claim->phone, $knownEmails, $knownPhones, 'claim_'.$claim->id);
                if ($key !== null) {
                    $guestKeys[$key] = true;
                }
            }
        }

        return count($guestKeys);
    }

    /**
     * @param  array<string, int>  $knownEmails
     * @param  array<string, int>  $knownPhones
     */
    protected function extractGuestLeadKey(?string $email, ?string $phone, array $knownEmails, array $knownPhones, string $fallbackId): ?string
    {
        $cleanEmail = strtolower(trim((string) $email));
        $cleanPhone = $this->normalizePhoneKey((string) $phone);

        if ($cleanEmail !== '' && isset($knownEmails[$cleanEmail])) {
            return null;
        }
        if ($cleanPhone !== '' && isset($knownPhones[$cleanPhone])) {
            return null;
        }

        if ($cleanEmail !== '') {
            return 'email:'.$cleanEmail;
        }
        if ($cleanPhone !== '') {
            return 'phone:'.$cleanPhone;
        }

        return 'anon:'.$fallbackId;
    }

    protected function normalizePhoneKey(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone) ?? '';
        if (str_starts_with($digits, '62')) {
            return substr($digits, 2);
        }
        if (str_starts_with($digits, '0')) {
            return substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Jawab konsultasi/inquiry klien serta perbarui status penanganan.
     */
    public function replyConsultation(Request $request, int $id): RedirectResponse
    {
        $consultation = FreeConsultation::find($id);
        if (! $consultation) {
            return back()->with('error', 'Data konsultasi atau inquiry tidak ditemukan.');
        }

        $request->merge([
            'status' => $request->input('status', 'responded'),
        ]);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,responded'],
            'response' => ['required_if:status,responded', 'nullable', 'string', 'max:3000'],
        ], [
            'response.required_if' => 'Tanggapan resmi wajib diisi ketika menandai pesan sudah dijawab.',
        ]);

        $responseText = trim((string) ($validated['response'] ?? ''));

        $consultation->update([
            'response' => $responseText !== '' ? $responseText : $consultation->response,
            'status' => $validated['status'],
        ]);

        $msg = $validated['status'] === 'responded'
            ? 'Tanggapan berhasil disimpan dan status diperbarui menjadi Sudah Dijawab.'
            : 'Status pertanyaan berhasil diperbarui menjadi Menunggu / Diproses.';

        return back()->with('success', $msg);
    }

    /**
     * Tambah kode kupon promo baru dengan validasi lengkap.
     */
    public function storePromo(Request $request): RedirectResponse
    {
        $payload = $this->validatePromoPayload($request, null, true);

        $promo = Promo::create(array_merge($payload, [
            'used_count' => 0,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]));

        return back()->with('success', 'Kode kupon promo '.$promo->code.' berhasil diterbitkan dengan kuota '.$promo->quota.' transaksi.');
    }

    /**
     * Perbarui data voucher lengkap maupun toggle status/kuota cepat.
     */
    public function updatePromo(Request $request, int $id): RedirectResponse
    {
        $promo = Promo::find($id);
        if (! $promo) {
            return back()->with('error', 'Data kupon promo tidak ditemukan.');
        }

        if ($request->has('discount_type')) {
            $payload = $this->validatePromoPayload($request, $promo->id, false);
            $promo->update(array_merge($payload, [
                'is_active' => $request->boolean('is_active'),
            ]));

            return back()->with('success', 'Detail kupon promo '.$promo->code.' berhasil diperbarui.');
        }

        $validated = $request->validate([
            'quota' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
            'min_transaction' => ['nullable', 'numeric', 'min:0'],
        ]);

        $updateData = [
            'quota' => (int) $validated['quota'],
            'is_active' => (bool) $validated['is_active'],
        ];

        if ($request->has('min_transaction')) {
            $updateData['min_transaction'] = (float) ($validated['min_transaction'] ?? 0);
        }

        $promo->update($updateData);

        return back()->with('success', 'Pengaturan kupon promo '.$promo->code.' berhasil diperbarui.');
    }

    /**
     * Hapus kupon promo.
     */
    public function destroyPromo(int $id): RedirectResponse
    {
        $promo = Promo::find($id);
        if (! $promo) {
            return back()->with('error', 'Data kupon promo tidak ditemukan.');
        }

        $code = $promo->code;
        $promo->delete();

        return back()->with('success', 'Kupon promo '.$code.' berhasil dihapus dari sistem.');
    }

    /**
     * Validasi dan normalisasi payload pembuatan/pengeditan voucher promosi.
     *
     * @return array<string, mixed>
     */
    protected function validatePromoPayload(Request $request, ?int $ignorePromoId, bool $requireFutureExpiry): array
    {
        $normalizedCode = strtoupper(trim((string) $request->input('code', '')));
        $request->merge(['code' => $normalizedCode]);

        $expiryRules = ['nullable', 'date'];
        if ($requireFutureExpiry) {
            $expiryRules[] = 'after_or_equal:today';
        }

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('promos', 'code')->ignore($ignorePromoId),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['required', 'in:nominal,percentage'],
            'discount_value' => [
                'required',
                'numeric',
                'min:1',
                function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                    if ($request->input('discount_type') === 'percentage' && (float) $value > 100) {
                        $fail('Persentase diskon tidak boleh melebihi 100%.');
                    }
                },
            ],
            'min_transaction' => ['nullable', 'numeric', 'min:0'],
            'quota' => ['required', 'integer', 'min:1'],
            'expired_date' => $expiryRules,
        ], [
            'code.required' => 'Kode promo wajib diisi.',
            'code.regex' => 'Kode promo hanya boleh berisi huruf kapital, angka, strip (-), atau underscore (_).',
            'code.unique' => 'Kode promo ini sudah terdaftar dalam sistem.',
            'discount_value.min' => 'Nilai diskon harus lebih besar dari 0.',
            'quota.min' => 'Batas kuota transaksi minimal 1.',
            'expired_date.after_or_equal' => 'Tanggal kedaluwarsa tidak boleh sebelum hari ini.',
        ]);

        $isNominal = $validated['discount_type'] === 'nominal';

        return [
            'code' => $validated['code'],
            'title' => ! empty($validated['title']) ? trim($validated['title']) : ('Voucher Promo '.$validated['code']),
            'discount_amount' => $isNominal ? (float) $validated['discount_value'] : 0.00,
            'discount_percentage' => ! $isNominal ? (float) $validated['discount_value'] : null,
            'min_transaction' => (float) ($validated['min_transaction'] ?? 0),
            'quota' => (int) $validated['quota'],
            'expired_date' => $validated['expired_date'] ?? null,
        ];
    }

    /**
     * Moderasi ulasan klien (Setujui / Pending / Tolak / Unggulkan).
     */
    public function moderateReview(Request $request, int $id): RedirectResponse
    {
        $review = Review::find($id);
        if (! $review) {
            return back()->with('error', 'Data ulasan pelanggan tidak ditemukan.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:published,approved,pending,rejected'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $targetStatus = $validated['status'] === 'approved' ? 'published' : $validated['status'];

        $review->update([
            'status' => $targetStatus,
            'is_featured' => $targetStatus === 'published' ? $request->boolean('is_featured') : false,
        ]);

        $msg = match ($targetStatus) {
            'published' => 'Ulasan disetujui dan ditampilkan sebagai testimoni di halaman publik.',
            'rejected' => 'Ulasan ditolak dan disembunyikan dari halaman testimoni publik.',
            default => 'Status ulasan diubah menjadi Menunggu Review.',
        };

        return back()->with('success', $msg);
    }

    /**
     * Bangun data riwayat penggunaan kupon beserta opsi filter dari transaksi order asli.
     *
     * @param  Collection<int, Promo>  $promos
     * @return array<string, mixed>
     */
    protected function buildPromoUsageHistory(Request $request, Collection $promos): array
    {
        $rawStatus = strtolower(trim((string) $request->query('usage_status', '')));
        $normalizedStatus = match ($rawStatus) {
            'berhasil', 'success' => 'berhasil',
            'batal', 'cancelled' => 'batal',
            default => '',
        };

        $historyFilters = [
            'promo_code' => strtoupper(trim((string) $request->query('promo_code', ''))),
            'client' => trim((string) $request->query('client', '')),
            'company' => trim((string) $request->query('company', '')),
            'service_id' => $request->filled('service_id') && is_numeric($request->query('service_id'))
                ? (int) $request->query('service_id')
                : null,
            'date_from' => $this->parseDateFilter($request->query('date_from')),
            'date_to' => $this->parseDateFilter($request->query('date_to')),
            'usage_status' => $normalizedStatus,
        ];

        $baseQuery = Order::query()
            ->where(function (Builder $q): void {
                $q->whereNotNull('promo_id')
                    ->orWhere(function (Builder $q2): void {
                        $q2->whereNotNull('promo_code')->where('promo_code', '!=', '');
                    });
            });

        $this->applyPromoUsageFilters($baseQuery, $historyFilters);

        $historySummary = [
            'totalCount' => (clone $baseQuery)->count(),
            'successCount' => (clone $baseQuery)->where('status', '!=', 'cancelled')->count(),
            'cancelledCount' => (clone $baseQuery)->where('status', 'cancelled')->count(),
            'totalDiscount' => (float) (clone $baseQuery)->where('status', '!=', 'cancelled')->sum('discount_amount'),
            'totalNetAmount' => (float) (clone $baseQuery)->where('status', '!=', 'cancelled')->sum('total_cost'),
        ];

        $promoUsages = (clone $baseQuery)
            ->with(['user', 'service', 'promo', 'invoice', 'latestPayment', 'items.service'])
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString()
            ->fragment('riwayat-penggunaan');

        $promoUsages->through(fn (Order $order): object => $this->formatPromoUsageRow($order));

        $orderPromoCodes = Order::query()
            ->whereNotNull('promo_code')
            ->where('promo_code', '!=', '')
            ->distinct()
            ->pluck('promo_code')
            ->map(fn (string $code): string => strtoupper(trim($code)));

        $availablePromoCodes = $promos->pluck('code')
            ->map(fn (string $code): string => strtoupper(trim($code)))
            ->merge($orderPromoCodes)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $services = Service::orderBy('name')->get(['id', 'name']);

        return compact(
            'promoUsages',
            'historyFilters',
            'historySummary',
            'availablePromoCodes',
            'services'
        );
    }

    /**
     * Terapkan filter kode voucher, klien, PT/perusahaan, layanan, tanggal, dan status pada query riwayat kupon.
     *
     * @param  array<string, mixed>  $filters
     */
    protected function applyPromoUsageFilters(Builder $query, array $filters): void
    {
        if ($filters['promo_code'] !== '') {
            $code = $filters['promo_code'];
            $query->where(function (Builder $q) use ($code): void {
                $q->where('promo_code', $code)
                    ->orWhereHas('promo', function (Builder $pq) use ($code): void {
                        $pq->where('code', $code);
                    });
            });
        }

        if ($filters['client'] !== '') {
            $like = '%'.$this->escapeLike($filters['client']).'%';
            $query->where(function (Builder $q) use ($like): void {
                $q->whereHas('user', function (Builder $uq) use ($like): void {
                    $uq->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like);
                })->orWhereHas('invoice', function (Builder $iq) use ($like): void {
                    $iq->where('customer_name', 'like', $like);
                });
            });
        }

        if ($filters['company'] !== '') {
            $like = '%'.$this->escapeLike($filters['company']).'%';
            $query->where(function (Builder $q) use ($like): void {
                $q->whereHas('user', function (Builder $uq) use ($like): void {
                    $uq->where('company_name', 'like', $like);
                })->orWhereHas('invoice', function (Builder $iq) use ($like): void {
                    $iq->where('customer_company', 'like', $like);
                });
            });
        }

        if ($filters['service_id'] !== null) {
            $serviceId = $filters['service_id'];
            $query->where(function (Builder $q) use ($serviceId): void {
                $q->where('service_id', $serviceId)
                    ->orWhereHas('items', function (Builder $iq) use ($serviceId): void {
                        $iq->where('service_id', $serviceId);
                    });
            });
        }

        if ($filters['date_from'] !== '') {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to'] !== '') {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if ($filters['usage_status'] === 'berhasil') {
            $query->where('status', '!=', 'cancelled');
        } elseif ($filters['usage_status'] === 'batal') {
            $query->where('status', 'cancelled');
        }
    }

    /**
     * Format baris transaksi penggunaan kupon dari model Order beserta relasi Klien, Layanan, Invoice, dan Pembayaran.
     */
    protected function formatPromoUsageRow(Order $order): object
    {
        $itemServices = $order->items
            ->map(fn ($item): ?string => $item->service?->name)
            ->filter()
            ->unique()
            ->values();

        $serviceName = $itemServices->isNotEmpty()
            ? $itemServices->implode(', ')
            : ($order->service?->name ?? $order->invoice?->service_name ?? '-');

        $discountAmount = (float) ($order->discount_amount > 0
            ? $order->discount_amount
            : ($order->invoice?->discount_amount ?? 0));
        $finalAmount = (float) $order->total_cost;
        $transactionAmount = (float) $order->base_price > 0
            ? (float) $order->base_price
            : ((float) ($order->invoice?->subtotal ?? 0) > 0
                ? (float) $order->invoice->subtotal
                : ($finalAmount + $discountAmount));

        $usedAt = $order->created_at?->copy()->timezone('Asia/Jakarta')->locale('id');

        return (object) [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'promo_code' => strtoupper((string) ($order->promo_code ?: $order->promo?->code ?: '-')),
            'promo_title' => $order->promo?->title ?? '',
            'client_name' => $order->invoice?->customer_name ?: ($order->user?->name ?? 'Pelanggan'),
            'client_email' => $order->invoice?->customer_email ?: ($order->user?->email ?? ''),
            'company_name' => $order->invoice?->customer_company ?: ($order->user?->company_name ?: '-'),
            'service_name' => $serviceName,
            'invoice_number' => $order->invoice?->invoice_number ?? '-',
            'transaction_amount' => $transactionAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'date_label' => $usedAt ? $usedAt->translatedFormat('d F Y') : '-',
            'time_label' => $usedAt ? $usedAt->format('H:i').' WIB' : '-',
            'is_cancelled' => $order->status === 'cancelled',
            'usage_status_label' => $order->status === 'cancelled' ? 'Batal' : 'Berhasil',
            'order_status_label' => $order->statusLabel(),
            'payment_status_label' => $order->paymentStatusMeta()['label'],
        ];
    }

    /**
     * Validasi format tanggal Y-m-d untuk filter riwayat agar input tidak valid tidak memicu galat.
     */
    protected function parseDateFilter(mixed $raw): string
    {
        $value = trim((string) $raw);
        if ($value === '') {
            return '';
        }

        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $value);

            return $parsed !== false && $parsed->format('Y-m-d') === $value ? $value : '';
        } catch (\Throwable) {
            return '';
        }
    }

    /**
     * Escape karakter wildcard LIKE SQL.
     */
    protected function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    /**
     * Tampilkan halaman profil & pengaturan akun staf Marketing yang sedang login.
     */
    public function profile(Request $request): View
    {
        $user = $request->user();
        $promos = Promo::orderByDesc('created_at')->get();
        $stats = $this->buildOperationalStats($promos);

        return view('marketing.profile', compact('user', 'stats'));
    }

    /**
     * Perbarui data profil milik akun Marketing yang sedang login (tanpa izin ubah role/hak akses).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->normalizeBaseProfileInputs($request);
        $rawCompany = trim((string) $request->input('company_name', ''));
        $rawAddress = trim((string) $request->input('address', ''));
        $rawPosition = trim((string) preg_replace('/\s+/', ' ', (string) $request->input('pic_position', '')));

        $request->merge([
            'company_name' => $rawCompany !== '' ? $rawCompany : null,
            'address' => $rawAddress !== '' ? $rawAddress : null,
            'pic_position' => $rawPosition !== '' ? $rawPosition : null,
        ]);

        $validated = $request->validate([
            'name' => $this->profileNameRules(true),
            'email' => $this->profileEmailRules($user->id),
            'phone' => $this->profilePhoneRules($user->id),
            'pic_position' => ['nullable', 'string', 'max:100'],
            'company_name' => [
                'nullable',
                'string',
                'min:3',
                'max:150',
                'regex:/^(?=.*[\pL])[a-zA-Z0-9\s\.\,\-\/\&\'\(\)]+$/u',
            ],
            'address' => ['nullable', 'string', 'min:5', 'max:500'],
        ], array_merge($this->baseProfileMessages(), [
            'company_name.min' => 'Nama perusahaan / unit kerja minimal 3 karakter.',
            'company_name.regex' => 'Format nama perusahaan / unit kerja tidak valid.',
            'address.min' => 'Alamat minimal 5 karakter.',
        ]));

        // Whitelist eksplisit: jangan pernah izinkan ubah role, permission, atau status verifikasi dari form profil
        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $this->normalizePhoneToLocal($validated['phone']),
            'pic_position' => $validated['pic_position'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'address' => $validated['address'] ?? null,
        ])->save();

        return redirect()->route('marketing.profile.edit')
            ->with('success', 'Data profil akun Marketing berhasil diperbarui.');
    }

    /**
     * Perbarui kata sandi milik akun Marketing yang sedang login.
     */
    public function updateProfilePassword(Request $request): RedirectResponse
    {
        return $this->performPasswordUpdate($request, 'marketing.profile.edit');
    }
}
