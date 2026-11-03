<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan daftar akun pengguna staf internal.
     */
    public function index(Request $request)
    {
        $roleFilter = $request->get('role');
        $search = $request->get('search');

        $query = User::whereIn('role', ['marketing', 'legal', 'finance', 'officer', 'konsultan', 'petugas', 'direktur', 'admin']);

        if ($roleFilter && $roleFilter !== 'all') {
            $query->where('role', $roleFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $staffUsers = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'totalStaff' => User::whereIn('role', ['marketing', 'legal', 'finance', 'officer', 'konsultan', 'petugas'])->count(),
            'marketing'  => User::where('role', 'marketing')->count(),
            'legal'      => User::whereIn('role', ['legal', 'konsultan'])->count(),
            'finance'    => User::whereIn('role', ['finance', 'officer'])->count(),
            'admin'      => User::whereIn('role', ['admin', 'direktur'])->count(),
        ];

        return view('admin.users.index', compact('staffUsers', 'stats', 'roleFilter', 'search'));
    }

    /**
     * Simpan pengguna staf internal baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'role'     => 'required|string|in:marketing,legal,finance,officer,konsultan,direktur,admin',
            'phone'    => 'nullable|string|max:30',
            'company'  => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
        ], [
            'name.required'     => 'Nama staf wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah digunakan oleh akun lain.',
            'role.required'     => 'Peran staf wajib dipilih.',
            'password.required' => 'Password staf wajib diisi minimal 8 karakter.',
        ]);

        User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'role'              => $validated['role'],
            'phone'             => $validated['phone'] ?? null,
            'company_name'      => $validated['company'] ?? 'CV Prasta Solusi Indonesia',
            'password'          => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Akun staf internal ' . $validated['name'] . ' berhasil ditambahkan.');
    }

    /**
     * Perbarui data staf internal.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'     => 'required|string|in:marketing,legal,finance,officer,konsultan,direktur,admin',
            'phone'    => 'nullable|string|max:30',
            'password' => 'nullable|string|min:8',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->phone = $validated['phone'] ?? $user->phone;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Data akun staf ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Hapus akun staf internal.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun staf ' . $name . ' berhasil dihapus dari sistem.');
    }
}
