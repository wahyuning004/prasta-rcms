@if(session('success'))
    <div class="px-8 pt-4">
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between text-emerald-800 text-sm shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </div>
                <div>
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="ml-1">{{ session('success') }}</span>
                </div>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="px-8 pt-4">
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center justify-between text-rose-800 text-sm shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                </div>
                <div>
                    <strong class="font-bold">Perhatian:</strong>
                    <span class="ml-1">{{ session('error') }}</span>
                </div>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-rose-500 hover:text-rose-700 p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
@endif

@if($errors->any())
    <div class="px-8 pt-4">
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-amber-900 text-sm shadow-sm animate-fade-in">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0 mt-0.5">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
                <div class="flex-1">
                    <strong class="font-bold block mb-1">Terdapat kesalahan validasi formulir:</strong>
                    <ul class="list-disc list-inside space-y-0.5 text-xs text-amber-800">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-amber-500 hover:text-amber-700 p-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>
@endif

