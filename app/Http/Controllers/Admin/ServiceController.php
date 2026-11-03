<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Tampilkan daftar master layanan perizinan & sertifikasi.
     */
    public function index(Request $request)
    {
        $query = Service::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('requirements_info', 'like', "%{$search}%");
            });
        }

        $services = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.services.index', compact('services', 'categories'));
    }

    /**
     * Tampilkan form pembuatan layanan baru.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.services.create', compact('categories'));
    }

    /**
     * Simpan layanan baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'         => ['required', 'exists:categories,id'],
            'name'                => ['required', 'string', 'max:255'],
            'slug'                => ['nullable', 'string', 'max:255', 'unique:services,slug'],
            'description'         => ['nullable', 'string'],
            'requirements_info'   => ['nullable', 'string'],
            'timeline_estimation' => ['nullable', 'string', 'max:255'],
            'price'               => ['required', 'numeric', 'min:0'],
        ], [
            'category_id.required' => 'Pilih kategori layanan yang sesuai.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',
            'name.required'        => 'Nama layanan perizinan & sertifikasi wajib diisi.',
            'slug.unique'          => 'Slug layanan ini sudah digunakan.',
            'price.required'       => 'Biaya estimasi layanan wajib diisi.',
            'price.numeric'        => 'Biaya harus berupa nominal angka valid.',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Service::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Layanan perizinan & sertifikasi "' . $validated['name'] . '" berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit layanan.
     */
    public function edit(Service $service)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.services.edit', compact('service', 'categories'));
    }

    /**
     * Perbarui data layanan di database.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'category_id'         => ['required', 'exists:categories,id'],
            'name'                => ['required', 'string', 'max:255'],
            'slug'                => ['nullable', 'string', 'max:255', 'unique:services,slug,' . $service->id],
            'description'         => ['nullable', 'string'],
            'requirements_info'   => ['nullable', 'string'],
            'timeline_estimation' => ['nullable', 'string', 'max:255'],
            'price'               => ['required', 'numeric', 'min:0'],
        ], [
            'category_id.required' => 'Pilih kategori layanan yang sesuai.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',
            'name.required'        => 'Nama layanan wajib diisi.',
            'slug.unique'          => 'Slug layanan ini sudah digunakan oleh layanan lain.',
            'price.required'       => 'Biaya estimasi layanan wajib diisi.',
            'price.numeric'        => 'Biaya harus berupa nominal angka valid.',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Layanan perizinan & sertifikasi "' . $service->name . '" berhasil diperbarui.');
    }

    /**
     * Hapus layanan dari database.
     */
    public function destroy(Service $service)
    {
        $name = $service->name;
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Layanan perizinan & sertifikasi "' . $name . '" berhasil dihapus.');
    }
}
