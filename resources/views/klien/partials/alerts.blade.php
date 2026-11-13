<!-- Flash Messages -->
<div class="px-4 sm:px-8 pt-4 space-y-2">
    @if (session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-sm font-medium shadow-sm">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('info'))
        <div class="flex items-center gap-3 bg-sky-50 border border-sky-200 text-sky-800 px-4 py-3 rounded-2xl text-sm font-medium shadow-sm">
            <i data-lucide="info" class="w-5 h-5 text-sky-600 flex-shrink-0"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-sm font-medium shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="flex items-start gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-sm shadow-sm">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="font-bold mb-1">Periksa kembali data yang Anda masukkan:</p>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>

