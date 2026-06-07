@extends('layouts.mobile')

@section('title', 'Dashboard – AbsensiKu')

@section('content')

{{-- ── Blue Greeting Hero (full-width) ─────────────────────────────────── --}}
<div class="bg-blue-600 px-4 pt-4 pb-10 shadow-md">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-blue-200 text-xs font-medium">Selamat datang,</p>
            <p class="text-white font-extrabold text-xl mt-0.5 truncate">{{ $mahasiswa->user->name }}</p>
            <p class="text-blue-200 text-xs mt-1">{{ $mahasiswa->nim }} &bull; {{ $mahasiswa->kelas->nama ?? '–' }}</p>
        </div>
    </div>

    {{-- Date card --}}
    <div class="mt-4 flex items-center gap-2 bg-blue-500/50 rounded-xl px-3 py-2.5">
        <i class="fas fa-clock text-blue-200 text-sm shrink-0"></i>
        <div>
            <p class="text-white font-semibold text-sm">{{ $tanggal }}</p>
            <p class="text-blue-200 text-xs">Panel Mahasiswa Absensi</p>
        </div>
    </div>
</div>

{{-- ── Content Area ─────────────────────────────────────────────────────── --}}
<div class="px-4 space-y-4 -mt-4 pb-6">

    {{-- ── Statistik Kehadiran Card ─────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-extrabold text-slate-800">Statistik Kehadiran</p>
        </div>

        <div class="flex items-end gap-2 mb-2">
            <p class="text-4xl font-extrabold text-blue-600">{{ $persentase }}%</p>
            <p class="text-xs text-slate-400 mb-1.5">dari {{ $totalAbsensi }} pertemuan</p>
        </div>

        <div class="w-full bg-slate-100 rounded-full h-2.5 mb-4">
            <div class="h-2.5 rounded-full bg-blue-500 transition-all" style="width: {{ $persentase }}%"></div>
        </div>

        <div class="grid grid-cols-3 gap-3 text-center">
            <div class="bg-emerald-50 border border-emerald-100 rounded-xl py-2.5">
                <p class="text-xl font-extrabold text-emerald-600">{{ $totalHadir }}</p>
                <p class="text-[11px] text-emerald-500 font-semibold mt-0.5">Hadir</p>
            </div>
            <div class="bg-amber-50 border border-amber-100 rounded-xl py-2.5">
                <p class="text-xl font-extrabold text-amber-500">{{ $totalIzin }}</p>
                <p class="text-[11px] text-amber-400 font-semibold mt-0.5">Izin</p>
            </div>
            <div class="bg-rose-50 border border-rose-100 rounded-xl py-2.5">
                <p class="text-xl font-extrabold text-rose-500">{{ $totalAlpha }}</p>
                <p class="text-[11px] text-rose-400 font-semibold mt-0.5">Alpha</p>
            </div>
        </div>
    </div>

    {{-- ── Two Action Buttons ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('mahasiswa.scan') }}"
           class="bg-blue-600 hover:bg-blue-700 rounded-2xl p-4 flex flex-col items-center gap-2 shadow-md transition">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-qrcode text-white text-2xl"></i>
            </div>
            <p class="text-white font-bold text-sm">Scan QR</p>
            <p class="text-blue-200 text-xs">Absen sekarang</p>
        </a>
        <a href="{{ route('mahasiswa.riwayat') }}"
           class="bg-white border-2 border-blue-100 hover:bg-blue-50 rounded-2xl p-4 flex flex-col items-center gap-2 transition">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-history text-blue-500 text-2xl"></i>
            </div>
            <p class="text-blue-600 font-bold text-sm">Riwayat</p>
            <p class="text-slate-400 text-xs">Lihat absensi</p>
        </a>
    </div>

    {{-- ── Jadwal Hari Ini ──────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-extrabold text-slate-800">Jadwal Hari Ini</p>
            <span class="text-xs font-semibold text-slate-400">{{ now()->isoFormat('ddd, D MMM') }}</span>
        </div>

        @forelse($jadwalHariIni as $jadwal)
            @php
                $jamMulai   = \Carbon\Carbon::createFromTimeString($jadwal->jam_mulai);
                $jamSelesai = \Carbon\Carbon::createFromTimeString($jadwal->jam_selesai);
                $sekarang   = now();
                $isAktif       = $sekarang->between($jamMulai, $jamSelesai);
                $isAkanDatang  = $sekarang->lt($jamMulai);
            @endphp

            <div class="flex items-center gap-3 py-2.5 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                {{-- Time --}}
                <p class="text-xs font-bold text-slate-400 w-10 shrink-0">
                    {{ $jamMulai->format('H.i') }}
                </p>
                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">
                        {{ $jadwal->mataKuliah->nama ?? '–' }}
                    </p>
                </div>
                {{-- Status badge --}}
                @if($isAktif)
                    <span class="shrink-0 px-2.5 py-0.5 bg-emerald-100 text-emerald-600 rounded-full text-[11px] font-bold">Aktif</span>
                @elseif($isAkanDatang)
                    <span class="shrink-0 px-2.5 py-0.5 bg-slate-100 text-slate-500 rounded-full text-[11px] font-bold">Akan Datang</span>
                @else
                    <span class="shrink-0 px-2.5 py-0.5 bg-slate-50 text-slate-400 rounded-full text-[11px] font-bold">Selesai</span>
                @endif
            </div>
        @empty
            <div class="py-8 flex flex-col items-center text-slate-400">
                <i class="fas fa-calendar-times text-3xl text-slate-200 mb-2"></i>
                <p class="text-sm text-slate-500">Tidak ada jadwal hari ini</p>
            </div>
        @endforelse
    </div>

</div>

@endsection
