@extends('layouts.mobile')

@section('title', 'Riwayat Absensi – AbsensiKu')

@section('content')
<div class="px-4 pt-4 space-y-4">

    {{-- ── Page Header ──────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}"
           class="w-9 h-9 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-base font-extrabold text-slate-800">Riwayat Absensi</h1>
            <p class="text-xs text-slate-500">{{ $sesis->total() }} sesi tercatat</p>
        </div>
    </div>

    {{-- ── Search Bar ───────────────────────────────────────────────────── --}}
    <form action="{{ route('admin.riwayat') }}" method="GET" id="filter-form">
        <div class="relative">
            <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-search text-sm"></i>
            </span>
            <input type="text" name="search" value="{{ $search }}"
                class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-400"
                placeholder="Cari mata kuliah atau kelas..."
                oninput="debounceSubmit()">
            @if($kelasId)
                <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
            @endif
            @if($mkId)
                <input type="hidden" name="mk_id" value="{{ $mkId }}">
            @endif
        </div>
    </form>

    {{-- ── Filter: Kelas Tabs ───────────────────────────────────────────── --}}
    <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1 scrollbar-hide">
        <a href="{{ route('admin.riwayat', array_filter(['search' => $search, 'mk_id' => $mkId])) }}"
           class="shrink-0 px-4 py-1.5 rounded-full text-xs font-bold border transition
                  {{ !$kelasId ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300' }}">
            Semua Kelas
        </a>
        @foreach($kelasList as $k)
            <a href="{{ route('admin.riwayat', array_filter(['search' => $search, 'mk_id' => $mkId, 'kelas_id' => $k->id])) }}"
               class="shrink-0 px-4 py-1.5 rounded-full text-xs font-bold border transition
                      {{ $kelasId == $k->id ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300' }}">
                {{ $k->nama }}
            </a>
        @endforeach
    </div>

    {{-- ── Filter: Mata Kuliah Chips ───────────────────────────────────── --}}
    @if($mkList->isNotEmpty())
    <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1 scrollbar-hide">
        <a href="{{ route('admin.riwayat', array_filter(['search' => $search, 'kelas_id' => $kelasId])) }}"
           class="shrink-0 px-4 py-1.5 rounded-full text-xs font-bold border transition
                  {{ !$mkId ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-slate-600 border-slate-200 hover:border-violet-300' }}">
            Semua MK
        </a>
        @foreach($mkList as $mk)
            <a href="{{ route('admin.riwayat', array_filter(['search' => $search, 'kelas_id' => $kelasId, 'mk_id' => $mk->id])) }}"
               class="shrink-0 px-4 py-1.5 rounded-full text-xs font-bold border transition
                      {{ $mkId == $mk->id ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-slate-600 border-slate-200 hover:border-violet-300' }}">
                {{ $mk->nama }}
            </a>
        @endforeach
    </div>
    @endif

    {{-- ── Sesi List ────────────────────────────────────────────────────── --}}
    @forelse($sesis as $sesi)
        @php
            $hadir = $sesi->absensis->where('status', 'hadir')->count();
            $total = $sesi->absensis->count();
            $persen = $total > 0 ? round($hadir / $total * 100) : 0;
            $isSelesai = $sesi->status === 'selesai';
        @endphp

        <a href="{{ route('admin.riwayat.show', $sesi) }}"
           class="block bg-white rounded-2xl shadow-sm border border-slate-100 p-4 hover:border-blue-200 transition">

            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-800 text-sm truncate">{{ $sesi->mataKuliah->nama ?? '–' }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $sesi->kelas->nama ?? '–' }}</p>
                    <p class="text-xs text-slate-400 mt-1">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ $sesi->started_at ? $sesi->started_at->isoFormat('ddd, D MMM YYYY • HH:mm') : '–' }}
                    </p>
                </div>
                <span class="shrink-0 px-2.5 py-1 rounded-full text-[11px] font-bold
                    {{ $isSelesai ? 'bg-slate-100 text-slate-500' : 'bg-emerald-100 text-emerald-600' }}">
                    {{ $isSelesai ? 'Selesai' : 'Aktif' }}
                </span>
            </div>

            <div class="mt-3">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-slate-500">Kehadiran</span>
                    <span class="text-xs font-bold text-slate-700">{{ $hadir }}/{{ $total }} ({{ $persen }}%)</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $persen }}%"></div>
                </div>
            </div>

            <div class="mt-2 flex items-center gap-3 text-[11px] text-slate-400">
                <span><i class="fas fa-user-check text-emerald-500 mr-1"></i>{{ $hadir }} hadir</span>
                <span><i class="fas fa-user-times text-rose-400 mr-1"></i>{{ $total - $hadir }} belum</span>
                <span class="ml-auto text-blue-500 font-semibold">Lihat detail <i class="fas fa-chevron-right text-[10px]"></i></span>
            </div>
        </a>
    @empty
        <div class="bg-white rounded-2xl border border-slate-100 py-16 flex flex-col items-center text-slate-400">
            <i class="fas fa-clipboard-list text-5xl text-slate-200 mb-3"></i>
            <p class="font-semibold text-slate-500">Belum ada riwayat</p>
            <p class="text-sm mt-1">{{ $search ? 'Coba kata kunci lain' : 'Buat sesi absensi terlebih dahulu' }}</p>
        </div>
    @endforelse

    @if($sesis->hasPages())
        <div class="pb-2">{{ $sesis->links() }}</div>
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
