@if(session('success'))
    <div class="px-8 pt-4">
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-sm font-medium shadow-sm">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="px-8 pt-4">
        <div class="flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-sm font-medium shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    </div>
@endif

@if(session('warning'))
    <div class="px-8 pt-4">
        <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-2xl text-sm font-medium shadow-sm">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0"></i>
            <span>{{ session('warning') }}</span>
        </div>
    </div>
@endif

@if(session('info'))
    <div class="px-8 pt-4">
        <div class="flex items-center gap-3 bg-sky-50 border border-sky-200 text-sky-800 px-4 py-3 rounded-2xl text-sm font-medium shadow-sm">
            <i data-lucide="info" class="w-5 h-5 text-sky-600 flex-shrink-0"></i>
            <span>{{ session('info') }}</span>
        </div>
    </div>
@endif

@if($errors->any())
    <div class="px-8 pt-4">
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-sm shadow-sm space-y-1">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                <span>Terdapat kesalahan pengisian data:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

