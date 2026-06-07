@extends('layouts.mobile')

@section('title', 'Riwayat Absensi – AbsensiKu')

@section('content')

{{-- ── Blue Header (full-width) ────────────────────────────────────────────── --}}
<div class="bg-blue-600 px-4 pt-4 pb-5 shadow-md">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}"
           class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center text-white hover:bg-white/30 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-base font-extrabold text-white">Riwayat Absensi</h1>
            <p class="text-xs text-blue-200">Data kehadiran semua mahasiswa</p>
        </div>
    </div>
</div>

<div class="px-4 pt-4 space-y-4 pb-6">

    {{-- ── Search Bar ───────────────────────────────────────────────────── --}}
    <form action="{{ route('admin.riwayat') }}" method="GET" id="filter-form">
        <div class="relative">
            <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-search text-sm"></i>
            </span>
            <input type="text" name="search" value="{{ $search }}"
                class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-400 shadow-sm"
                placeholder="Cari nama, NIM, atau mata kuliah..."
                oninput="debounceSubmit()">
            @if($kelasId)
                <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
            @endif
        </div>
    </form>

    {{-- ── Filter Chips (Kelas) ────────────────────────────────────────── --}}
    <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1 scrollbar-hide">
        <a href="{{ route('admin.riwayat', array_filter(['search' => $search])) }}"
           class="shrink-0 px-4 py-1.5 rounded-full text-xs font-bold border transition
                  {{ !$kelasId ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300' }}">
            Semua
        </a>
        @foreach($kelasList as $k)
            <a href="{{ route('admin.riwayat', array_filter(['search' => $search, 'kelas_id' => $k->id])) }}"
               class="shrink-0 px-4 py-1.5 rounded-full text-xs font-bold border transition
                      {{ $kelasId == $k->id ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300' }}">
                {{ $k->nama }}
            </a>
        @endforeach
    </div>

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

    {{-- ── Count --}}
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500 font-semibold">{{ $absensis->total() }} data ditemukan</p>
        <span class="text-xs text-blue-600 font-semibold">Filter ▾</span>
    </div>

    {{-- ── Absensi Records ──────────────────────────────────────────────── --}}
    @forelse($absensis as $abs)
        @php
            $colors   = ['bg-blue-500','bg-violet-500','bg-emerald-500','bg-amber-500','bg-rose-500','bg-cyan-500'];
            $color    = $colors[crc32($abs->mahasiswa->user->name ?? '') % count($colors)];
            $initials = collect(explode(' ', $abs->mahasiswa->user->name ?? '?'))
                            ->take(2)->map(fn($w) => strtoupper($w[0]))->join('');
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
        @endphp

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <div class="flex items-start gap-3">
                {{-- Avatar --}}
                <div class="w-11 h-11 {{ $color }} rounded-xl flex items-center justify-center text-white font-extrabold text-sm shrink-0 shadow-sm">
                    {{ $initials }}
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 text-sm truncate">{{ $abs->mahasiswa->user->name ?? '–' }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $abs->mahasiswa->nim ?? '–' }} &bull; {{ $abs->mahasiswa->kelas->nama ?? '–' }}
                            </p>
                        </div>
                        <span class="shrink-0 px-2.5 py-0.5 {{ $badgeClass }} rounded-full text-[11px] font-bold">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <p class="text-sm font-semibold text-blue-600 mt-2 truncate">
                        {{ $abs->sesi->mataKuliah->nama ?? '–' }}
                    </p>

                    <p class="text-xs text-slate-400 mt-0.5">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ $abs->waktu_scan ? $abs->waktu_scan->isoFormat('D MMM YYYY • HH:mm') : '–' }}
                    </p>

                    @if($abs->status === 'izin' && $abs->keterangan)
                        <p class="text-xs text-amber-600 bg-amber-50 rounded-lg px-2 py-1 mt-1.5 inline-block">
                            Keterangan: {{ $abs->keterangan }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-slate-100 py-16 flex flex-col items-center text-slate-400">
            <i class="fas fa-clipboard-list text-5xl text-slate-200 mb-3"></i>
            <p class="font-semibold text-slate-500">Belum ada riwayat absensi</p>
            <p class="text-sm mt-1">{{ $search ? 'Coba kata kunci lain' : 'Buat sesi absensi terlebih dahulu' }}</p>
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
