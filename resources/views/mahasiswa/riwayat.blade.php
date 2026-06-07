@extends('layouts.mobile')

@section('title', 'Riwayat Absensi – AbsensiKu')

@section('content')

{{-- ── Blue Header (full-width) ────────────────────────────────────────────── --}}
<div class="bg-blue-600 px-4 pt-4 pb-5 shadow-md">
    <div class="flex items-center gap-3">
        <a href="{{ route('mahasiswa.dashboard') }}"
           class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center text-white hover:bg-white/30 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-base font-extrabold text-white">Riwayat Absensi</h1>
            <p class="text-xs text-blue-200">History kehadiran anda</p>
        </div>
    </div>
</div>

<div class="px-4 pt-4 space-y-4 pb-6">

    {{-- ── Search Bar ───────────────────────────────────────────────────── --}}
    <form action="{{ route('mahasiswa.riwayat') }}" method="GET" id="filter-form">
        <div class="relative">
            <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-search text-sm"></i>
            </span>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-400 shadow-sm"
                placeholder="Cari mata kuliah..."
                oninput="debounceSubmit()">
        </div>
    </form>

    {{-- ── Stats Row ────────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-1.5 flex-1 bg-white border border-slate-100 rounded-xl px-3 py-2.5 shadow-sm">
            <div class="w-7 h-7 bg-emerald-100 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-check text-emerald-600 text-xs"></i>
            </div>
            <div>
                <p class="text-lg font-extrabold text-emerald-600 leading-none">{{ $totalHadir }}</p>
                <p class="text-[10px] text-slate-400 font-semibold">Hadir</p>
            </div>
        </div>
        <div class="flex items-center gap-1.5 flex-1 bg-white border border-slate-100 rounded-xl px-3 py-2.5 shadow-sm">
            <div class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-clock text-amber-500 text-xs"></i>
            </div>
            <div>
                <p class="text-lg font-extrabold text-amber-500 leading-none">{{ $totalIzin }}</p>
                <p class="text-[10px] text-slate-400 font-semibold">Izin</p>
            </div>
        </div>
        <div class="flex items-center gap-1.5 flex-1 bg-white border border-slate-100 rounded-xl px-3 py-2.5 shadow-sm">
            <div class="w-7 h-7 bg-rose-100 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-times text-rose-500 text-xs"></i>
            </div>
            <div>
                <p class="text-lg font-extrabold text-rose-500 leading-none">{{ $totalAlpha }}</p>
                <p class="text-[10px] text-slate-400 font-semibold">Alpha</p>
            </div>
        </div>
    </div>

    {{-- ── Count + Filter ──────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500 font-semibold">{{ $absensis->total() }} data ditemukan</p>
        <span class="text-xs text-blue-600 font-semibold">Filter ▾</span>
    </div>

    {{-- ── Absensi List ─────────────────────────────────────────────────── --}}
    @forelse($absensis as $abs)
        @php
            $iconClass = match($abs->status) {
                'hadir' => 'bg-emerald-100 text-emerald-600 fas fa-check',
                'izin'  => 'bg-amber-100 text-amber-500 fas fa-clock',
                default => 'bg-rose-100 text-rose-500 fas fa-times',
            };
            $badgeClass = match($abs->status) {
                'hadir' => 'bg-emerald-100 text-emerald-600',
                'izin'  => 'bg-amber-100 text-amber-600',
                default => 'bg-rose-100 text-rose-500',
            };
            $statusLabel = match($abs->status) {
                'hadir' => 'Hadir',
                'izin'  => 'Izin',
                default => 'Alpha',
            };
            $parts = explode(' ', $iconClass, 3);
            $iconBg   = $parts[0] . ' ' . $parts[1];
            $iconName = $parts[2];
        @endphp

        <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm flex items-center gap-3">
            {{-- Status icon circle --}}
            <div class="w-10 h-10 {{ $iconBg }} rounded-full flex items-center justify-center shrink-0">
                <i class="{{ $iconName }} text-sm"></i>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-800 text-sm truncate">
                    {{ $abs->sesi->mataKuliah->nama ?? '–' }}
                </p>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ $abs->waktu_scan ? $abs->waktu_scan->isoFormat('D MMM YYYY • HH:mm') : '–' }}
                </p>
            </div>

            {{-- Status badge --}}
            <span class="shrink-0 px-2.5 py-1 {{ $badgeClass }} rounded-full text-[11px] font-bold">
                {{ $statusLabel }}
            </span>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-slate-100 py-16 flex flex-col items-center text-slate-400">
            <i class="fas fa-clipboard-list text-5xl text-slate-200 mb-3"></i>
            <p class="font-semibold text-slate-500">Belum ada riwayat absensi</p>
            <p class="text-sm mt-1 text-slate-400">
                {{ $search ? 'Tidak ada hasil untuk "' . $search . '"' : 'Scan QR Code dosen untuk mulai absensi' }}
            </p>
        </div>
    @endforelse

    @if($absensis->hasPages())
        <div class="pb-2">{{ $absensis->links() }}</div>
    @endif

</div>

@endsection

@push('scripts')
<script>
    let searchTimer;
    function debounceSubmit() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => document.getElementById('filter-form').submit(), 500);
    }
</script>
@endpush
