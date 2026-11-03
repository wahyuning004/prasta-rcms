<?php

// Namespace controller mengikuti struktur folder App/Http/Controllers
namespace App\Http\Controllers;

// Import model yang dibutuhkan untuk query data
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    /**
     * Tampilkan landing page dengan data dinamis Kategori & Layanan Perizinan & Sertifikasi.
     */
    public function index(Request $request)
    {
        // Ambil kategori yang memiliki minimal 1 layanan, diurutkan alfabetis
        $categories = Category::withCount('services')
            ->having('services_count', '>', 0)
            ->orderBy('name')
            ->get();

        // Ambil semua layanan beserta relasi kategorinya, terbaru di atas
        $services = Service::with('category')
            ->latest()
            ->get();

        // Kirim data ke view landing page
        return view('landing', compact('categories', 'services'));
    }

    /**
     * Tampilkan halaman detail layanan perizinan & sertifikasi lengkap sesuai KAK.
     */
    public function showService($slug)
    {
        // Cari layanan berdasarkan slug atau ID (fallback), gagal = 404
        $service = Service::with('category')
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        // Ambil maksimal 3 layanan terkait (kategori sama, bukan layanan ini sendiri)
        $relatedServices = Service::where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->take(3)
            ->get();

        // Ambil semua kategori untuk keperluan navigasi/filter di view
        $categories = Category::withCount('services')->get();

        // Kirim data ke view detail layanan
        return view('services.show', compact('service', 'relatedServices', 'categories'));
    }

    /**
     * Simpan konsultasi kekurangan persyaratan dokumen dari halaman detail layanan.
     */
    public function submitDocConsultation(Request $request)
    {
        // Validasi field wajib dari form konsultasi dokumen
        $request->validate([
            'name'                 => 'required|string|max:255', // Nama PIC/penanggung jawab
            'phone'                => 'required|string|max:50',  // Nomor WhatsApp aktif
            'service_name'         => 'required|string|max:255', // Nama layanan yang dikonsultasikan
            'missing_doc_category' => 'required|string|max:255', // Jenis kendala/dokumen yang kurang
            'message'              => 'required|string',          // Keterangan detail kendala
        ]);

        // Jika user sudah login, simpan konsultasi ke tabel free_consultations
        if (auth()->check()) {
            \Illuminate\Support\Facades\DB::table('free_consultations')->insert([
                'user_id'    => auth()->id(),
                'subject'    => 'Kendala Dokumen: ' . $request->service_name . ' (' . $request->missing_doc_category . ')',
                'message'    => $request->message . "\n\nPIC: {$request->name}\nTelp/WA: {$request->phone}\nPerusahaan: " . ($request->company ?: '-'),
                'status'     => 'pending', // Status awal = menunggu respons tim
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Redirect kembali dengan pesan sukses flash
        return back()->with('success', 'Permintaan konsultasi persyaratan dokumen berhasil dikirim! Tim Legal & Regulatory kami akan segera menghubungi Anda.');
    }
}
