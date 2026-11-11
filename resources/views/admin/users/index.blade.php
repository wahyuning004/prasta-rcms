<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna Staf - IzinYou Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .text-corporate { color: #0f172a; }
        .bg-corporate { background-color: #0f172a; }
        .text-gold { color: #d4af37; }
        .bg-gold { background-color: #d4af37; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-sky-500 selection:text-white flex h-screen overflow-hidden">

    <!-- Sidebar Navigation -->
    @include('admin.partials.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/50">
        
        <!-- Top Header Bar -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 relative z-10">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-corporate">Dashboard</a>
                    <span>/</span>
                    <span class="text-sky-600 font-semibold">User Management</span>
                </div>
                <h2 class="text-xl font-bold text-corporate">Manajemen Pengguna Staf Internal</h2>
            </div>
            
            <div class="flex items-center gap-4">
                <button onclick="document.getElementById('addStaffModal').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-sm rounded-xl shadow-md shadow-sky-600/20 transition-all">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Tambah Akun Staf
                </button>
            </div>
        </header>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8">

            {{-- Alert Success --}}
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-sky-50 border border-sky-200 text-sky-800 px-5 py-4 rounded-2xl shadow-sm text-sm font-medium">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-sky-600 shrink-0"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            {{-- Alert Error --}}
            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl shadow-sm text-sm font-medium">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            {{-- Alert Validation Errors --}}
            @if($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl text-sm">
                    <p class="font-bold mb-1">Terdapat kesalahan pengisian data:</p>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 1. Stats Counter Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="text-xs font-semibold text-slate-400 mb-1">Total Staf Internal</p>
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-extrabold text-corporate">{{ $stats['totalStaff'] }}</h3>
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="text-xs font-semibold text-slate-400 mb-1">Marketing &amp; CR</p>
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-extrabold text-indigo-600">{{ $stats['marketing'] }}</h3>
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <i data-lucide="megaphone" class="w-5 h-5"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="text-xs font-semibold text-slate-400 mb-1">Legal Specialist</p>
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-extrabold text-sky-600">{{ $stats['legal'] }}</h3>
                        <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                            <i data-lucide="scale" class="w-5 h-5"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="text-xs font-semibold text-slate-400 mb-1">Finance &amp; Licensing</p>
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-extrabold text-amber-600">{{ $stats['finance'] }}</h3>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i data-lucide="wallet" class="w-5 h-5"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="text-xs font-semibold text-slate-400 mb-1">Direktur &amp; Admin</p>
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-extrabold text-slate-900">{{ $stats['admin'] }}</h3>
                        <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Filter & Search Bar -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-2">Filter Peran:</span>
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ empty($roleFilter) || $roleFilter === 'all' ? 'bg-corporate text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Staf
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'marketing']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $roleFilter === 'marketing' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Marketing
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'legal']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $roleFilter === 'legal' ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Legal Specialist
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'finance']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $roleFilter === 'finance' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Finance
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'officer']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $roleFilter === 'officer' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Licensing Officer
                    </a>
                </div>

                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2 w-full md:w-auto">
                    @if($roleFilter)
                        <input type="hidden" name="role" value="{{ $roleFilter }}">
                    @endif
                    <div class="relative flex-1 md:w-64">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, hp..." class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-sky-500">
                    </div>
                    <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-semibold rounded-xl hover:bg-slate-700">
                        Cari
                    </button>
                    @if($search)
                        <a href="{{ route('admin.users.index', ['role' => $roleFilter]) }}" class="px-2.5 py-1.5 text-xs text-rose-500 hover:bg-rose-50 rounded-xl">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- 3. Table Staf Internal -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-4 px-6">Nama &amp; Akun Staf</th>
                                <th class="py-4 px-6">Peran (Role Sistem)</th>
                                <th class="py-4 px-6">Kontak WhatsApp</th>
                                <th class="py-4 px-6">Hak Akses Panel</th>
                                <th class="py-4 px-6">Status 2FA</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($staffUsers as $staff)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm
                                                @if($staff->role === 'marketing') bg-indigo-100 text-indigo-700
                                                @elseif(in_array($staff->role, ['legal', 'konsultan'])) bg-sky-100 text-sky-700
                                                @elseif(in_array($staff->role, ['finance', 'officer'])) bg-amber-100 text-amber-700
                                                @else bg-slate-900 text-white
                                                @endif">
                                                {{ strtoupper(substr($staff->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-corporate">{{ $staff->name }}</p>
                                                <p class="text-xs text-slate-400">{{ $staff->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($staff->role === 'marketing')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                <i data-lucide="megaphone" class="w-3.5 h-3.5"></i> Marketing &amp; Client Relations
                                            </span>
                                        @elseif($staff->role === 'legal')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                                <i data-lucide="scale" class="w-3.5 h-3.5"></i> Legal &amp; Regulatory
                                            </span>
                                        @elseif($staff->role === 'finance')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                <i data-lucide="wallet" class="w-3.5 h-3.5"></i> Finance &amp; Administration
                                            </span>
                                        @elseif($staff->role === 'officer')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                <i data-lucide="award" class="w-3.5 h-3.5"></i> Licensing Officer
                                            </span>
                                        @elseif($staff->role === 'konsultan')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-200">
                                                <i data-lucide="briefcase" class="w-3.5 h-3.5"></i> Konsultan Teknis
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-900 text-white">
                                                <i data-lucide="shield" class="w-3.5 h-3.5"></i> {{ ucfirst($staff->role) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-slate-600 text-xs font-mono">
                                        {{ $staff->phone ?? '-' }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="text-xs font-semibold text-slate-500 font-mono">
                                            @if($staff->role === 'marketing')
                                                /marketing/*
                                            @elseif(in_array($staff->role, ['legal', 'konsultan']))
                                                /legal/*
                                            @elseif(in_array($staff->role, ['finance', 'officer']))
                                                /finance/*
                                            @else
                                                /admin/*
                                            @endif
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-sky-600 bg-sky-50 px-2.5 py-1 rounded-md border border-sky-100">
                                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> 2FA Aktif
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick="openEditModal({{ json_encode($staff) }})" class="p-2 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition-colors" title="Ubah Staf">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </button>

                                            @if($staff->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $staff->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun staf {{ $staff->name }}?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Staf">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        <i data-lucide="users" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                        <p>Tidak ada data staf internal yang ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($staffUsers->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $staffUsers->links() }}
                    </div>
                @endif
            </div>

        </div>
    </main>

    <!-- Modal Tambah Staf Baru -->
    <div id="addStaffModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in duration-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-corporate text-lg">Tambah Akun Staf Internal</h3>
                        <p class="text-xs text-slate-400">Buat kredensial akses untuk staf internal IzinYou</p>
                    </div>
                </div>
                <button onclick="document.getElementById('addStaffModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap &amp; Gelar <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Ari Agusta, S.Kom" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email Resmi Staf <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="staf@izinyou.com" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp Staf</label>
                        <input type="text" name="phone" placeholder="08xxxxxxxxxx" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Peran / Role Staf <span class="text-rose-500">*</span></label>
                        <select name="role" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500">
                            <option value="marketing">Marketing &amp; Client Relations</option>
                            <option value="legal">Legal &amp; Regulatory Specialist</option>
                            <option value="finance">Finance &amp; Administration</option>
                            <option value="officer">Licensing Officer</option>
                            <option value="konsultan">Konsultan Teknis</option>
                            <option value="direktur">Direktur Utama</option>
                            <option value="admin">Administrator Sistem</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Password Awal <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('addStaffModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                        Simpan Akun Staf
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Staf -->
    <div id="editStaffModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                        <i data-lucide="edit" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-corporate text-lg">Perbarui Data Akun Staf</h3>
                        <p class="text-xs text-slate-400">Ubah peran, email, atau reset password staf</p>
                    </div>
                </div>
                <button onclick="document.getElementById('editStaffModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="editStaffForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap &amp; Gelar</label>
                    <input type="text" id="editName" name="name" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email Resmi</label>
                        <input type="email" id="editEmail" name="email" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp</label>
                        <input type="text" id="editPhone" name="phone" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Peran / Role Staf</label>
                        <select id="editRole" name="role" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500">
                            <option value="marketing">Marketing &amp; Client Relations</option>
                            <option value="legal">Legal &amp; Regulatory Specialist</option>
                            <option value="finance">Finance &amp; Administration</option>
                            <option value="officer">Licensing Officer</option>
                            <option value="konsultan">Konsultan Teknis</option>
                            <option value="direktur">Direktur Utama</option>
                            <option value="admin">Administrator Sistem</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ganti Password (Opsional)</label>
                        <input type="password" name="password" minlength="8" placeholder="Kosongkan jika tak diubah" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('editStaffModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function openEditModal(staff) {
            document.getElementById('editName').value = staff.name;
            document.getElementById('editEmail').value = staff.email;
            document.getElementById('editPhone').value = staff.phone || '';
            document.getElementById('editRole').value = staff.role;
            document.getElementById('editStaffForm').action = "/admin/users/" + staff.id;
            document.getElementById('editStaffModal').classList.remove('hidden');
        }
    </script>
</body>
</html>
