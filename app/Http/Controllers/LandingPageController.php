<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FreeConsultation;
use App\Models\Promo;
use App\Models\Review;
use App\Models\Service;
use App\Services\ServiceCatalogService;
use App\Services\ServiceRequirementSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    /**
     * Tampilkan landing page dengan data dinamis Kategori, Layanan & Ulasan Terpublikasi.
     */
    public function index(Request $request): View
    {
        $categories = Category::withCount('services')
            ->has('services')
            ->orderBy('name')
            ->get();

        $priorityIds = [21, 22, 23, 7, 8, 9, 10, 11];
        $services = Service::with('category')
            ->get()
            ->sortBy(function ($service) use ($priorityIds) {
                $pos = array_search($service->id, $priorityIds, true);

                return $pos !== false ? $pos : 1000 + $service->id;
            })
            ->values();

        $publishedReviews = Review::with(['user', 'service'])
            ->where('status', 'published')
            ->latest()
            ->take(8)
            ->get();

        return view('landing', compact('categories', 'services', 'publishedReviews'));
    }

    /**
     * Tampilkan halaman Katalog Semua Layanan & Voucher Promo dari database.
     */
    public function catalog(Request $request, ServiceCatalogService $catalogService): View
    {
        return view('services.catalog', $catalogService->buildCatalogData($request));
    }

    /**
     * Tampilkan halaman detail layanan perizinan & sertifikasi lengkap beserta persyaratan & ulasan publik.
     */
    public function showService(
        string $slug,
        ServiceCatalogService $catalogService,
        ServiceRequirementSyncService $requirementSync
    ): View {
        $service = Service::with('category')
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $detailData = $catalogService->buildServiceDetailData($service, $requirementSync);
        $categories = Category::withCount('services')->get();

        return view('services.show', array_merge($detailData, compact('categories')));
    }

    /**
     * Simpan konsultasi kekurangan persyaratan dokumen dari halaman detail layanan ke database.
     */
    public function submitDocConsultation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'service_name' => ['required', 'string', 'max:255'],
            'missing_doc_category' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:3000'],
        ], [
            'name.required' => 'Nama PIC wajib diisi.',
            'phone.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'missing_doc_category.required' => 'Pilih jenis kendala dokumen.',
            'message.required' => 'Keterangan detail kendala wajib diisi.',
        ]);

        FreeConsultation::create([
            'user_id' => Auth::id(),
            'name' => trim($validated['name']),
            'email' => ! empty($validated['email']) ? trim($validated['email']) : Auth::user()?->email,
            'phone' => trim($validated['phone']),
            'subject' => 'Konsultasi Layanan: '.$validated['service_name'].' ('.$validated['missing_doc_category'].')',
            'message' => trim($validated['message'])
                ."\n\nPIC: {$validated['name']}\nTelp/WA: {$validated['phone']}\nPerusahaan: "
                .(! empty($validated['company']) ? $validated['company'] : '-'),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Permintaan konsultasi lanjutan berhasil dikirim dan tersimpan! Tim Legal & Regulatory kami akan segera menghubungi Anda.');
    }
}
