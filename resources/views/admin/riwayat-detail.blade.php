@extends('layouts.mobile')

@section('title', 'Detail Absensi – AbsensiKu')

@section('content')
<div class="px-4 pt-4 pb-4 space-y-4">

    {{-- ── Page Header (Blue) ─────────────────────────────────────────── --}}
    <div class="bg-blue-600 -mx-4 -mt-4 px-4 pt-4 pb-3 mb-1">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.riwayat') }}"
               class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center text-white hover:bg-white/30 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-base font-extrabold text-white">Detail Absensi</h1>
                <p class="text-xs text-blue-200">{{ $sesi->mataKuliah->nama ?? '–' }} – {{ $sesi->kelas->nama ?? '–' }}</p>
            </div>
        </div>
    </div>

    {{-- ── Info Card ───────────────────────────────────────────────────── --}}
    @php
        $hadir  = $sesi->absensis->where('status', 'hadir')->count();
        $izin   = $sesi->absensis->where('status', 'izin')->count();
        $alpha  = $sesi->absensis->where('status', 'alpha')->count();
        $total  = $sesi->absensis->count();
        $persen = $total > 0 ? round($hadir / $total * 100) : 0;
    @endphp

    <div class="bg-blue-600 rounded-2xl p-4 text-white shadow">
        <div class="flex items-start justify-between">
            <div>
                <p class="font-extrabold text-base">{{ $sesi->mataKuliah->nama ?? '–' }}</p>
                <p class="text-blue-200 text-sm mt-0.5">{{ $sesi->kelas->nama ?? '–' }}</p>
                <p class="text-blue-200 text-xs mt-1">
                    <i class="fas fa-calendar mr-1"></i>
                    {{ $sesi->started_at ? $sesi->started_at->isoFormat('ddd, D MMM YYYY • HH:mm') : '–' }}
                </p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                {{ $sesi->status === 'selesai' ? 'bg-white/20 text-white' : 'bg-emerald-400/30 text-emerald-200' }}">
                {{ $sesi->status === 'selesai' ? 'Selesai' : 'Aktif' }}
            </span>
        </div>
    </div>

    {{-- ── Rekap Statistik ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-4 gap-2">
        <div class="bg-emerald-50 border border-emerald-100 rounded-xl py-3 flex flex-col items-center">
            <span class="text-xl font-extrabold text-emerald-600">{{ $hadir }}</span>
            <span class="text-[10px] text-emerald-500 font-semibold mt-0.5">Hadir</span>
        </div>
        <div class="bg-amber-50 border border-amber-100 rounded-xl py-3 flex flex-col items-center">
            <span class="text-xl font-extrabold text-amber-600">{{ $izin }}</span>
            <span class="text-[10px] text-amber-500 font-semibold mt-0.5">Izin</span>
        </div>
        <div class="bg-rose-50 border border-rose-100 rounded-xl py-3 flex flex-col items-center">
            <span class="text-xl font-extrabold text-rose-500">{{ $alpha }}</span>
            <span class="text-[10px] text-rose-400 font-semibold mt-0.5">Alpha</span>
        </div>
        <div class="bg-blue-50 border border-blue-100 rounded-xl py-3 flex flex-col items-center">
            <span class="text-xl font-extrabold text-blue-600">{{ $persen }}%</span>
            <span class="text-[10px] text-blue-400 font-semibold mt-0.5">Hadir</span>
        </div>
    </div>

    {{-- ── Daftar Mahasiswa ─────────────────────────────────────────────── --}}
    <div class="space-y-3">
        <p class="text-sm font-bold text-slate-700">Daftar Kehadiran ({{ $total }} mahasiswa)</p>

        @forelse($sesi->absensis as $abs)
            @php
                $colors = ['bg-blue-500','bg-violet-500','bg-emerald-500','bg-amber-500','bg-rose-500','bg-cyan-500'];
                $color  = $colors[crc32($abs->mahasiswa->user->name ?? '') % count($colors)];
                $initials = collect(explode(' ', $abs->mahasiswa->user->name ?? '?'))->take(2)
                              ->map(fn($w) => strtoupper($w[0]))->join('');
            @endphp
            <div class="bg-white rounded-2xl border border-slate-100 p-3.5 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 {{ $color }} rounded-xl flex items-center justify-center text-white font-extrabold text-sm shrink-0">
                    {{ $initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-800 text-sm truncate">{{ $abs->mahasiswa->user->name ?? '–' }}</p>
                    <p class="text-xs text-slate-400">{{ $abs->mahasiswa->nim ?? '–' }}</p>
                </div>
                <div class="shrink-0 flex flex-col items-end gap-1">
                    @if($abs->status === 'hadir')
                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-600 rounded-full text-[11px] font-bold">Hadir</span>
                    @elseif($abs->status === 'izin')
                        <span class="px-2.5 py-0.5 bg-amber-100 text-amber-600 rounded-full text-[11px] font-bold">Izin</span>
                    @else
                        <span class="px-2.5 py-0.5 bg-rose-100 text-rose-500 rounded-full text-[11px] font-bold">Alpha</span>
                    @endif
                    @if($abs->waktu_scan)
                        <span class="text-[10px] text-slate-400">{{ $abs->waktu_scan->format('H:i') }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-100 py-12 flex flex-col items-center text-slate-400">
                <i class="fas fa-users text-4xl text-slate-200 mb-3"></i>
                <p class="text-slate-500 font-semibold">Belum ada data absensi</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
