@extends('layouts.mobile')

@section('title', 'Dashboard – AbsensiKu')

@section('content')
<div class="px-4 pt-4 space-y-4">

    {{-- ── Hero Card ────────────────────────────────────────────────────── --}}
    <div class="bg-blue-600 rounded-2xl p-5 shadow-md">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-white font-extrabold text-lg">
                {{ $mahasiswa->inisials }}
            </div>
            <div>
                <p class="text-white font-extrabold text-sm leading-tight">{{ $mahasiswa->user->name }}</p>
                <p class="text-blue-200 text-xs mt-0.5">{{ $mahasiswa->nim }}</p>
                <p class="text-blue-200 text-xs">{{ $mahasiswa->kelas->nama ?? '–' }} &bull; {{ $mahasiswa->prodi->nama ?? '–' }}</p>
            </div>
        </div>

        {{-- Kehadiran overall --}}
        <div class="bg-white/15 rounded-xl p-3">
            <div class="flex items-center justify-between mb-1.5">
                <p class="text-white text-xs font-semibold">Kehadiran Keseluruhan</p>
                <p class="text-white font-extrabold text-base">{{ $persentase }}%</p>
            </div>
            <div class="w-full bg-white/20 rounded-full h-2">
                <div class="h-2 rounded-full bg-white transition-all" style="width: {{ $persentase }}%"></div>
            </div>
        </div>
    </div>

    {{-- ── Stat Grid ────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white rounded-2xl border border-slate-100 p-3 shadow-sm text-center">
            <p class="text-xl font-extrabold text-emerald-600">{{ $totalHadir }}</p>
            <p class="text-[11px] text-slate-500 mt-0.5 font-semibold">Hadir</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-3 shadow-sm text-center">
            <p class="text-xl font-extrabold text-amber-500">{{ $totalIzin }}</p>
            <p class="text-[11px] text-slate-500 mt-0.5 font-semibold">Izin</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-3 shadow-sm text-center">
            <p class="text-xl font-extrabold text-rose-500">{{ $totalAlpha }}</p>
            <p class="text-[11px] text-slate-500 mt-0.5 font-semibold">Alpha</p>
        </div>
    </div>

    {{-- ── Jadwal Hari Ini ──────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-extrabold text-slate-800">Jadwal Hari Ini</p>
            <span class="text-xs text-blue-600 font-semibold">{{ now()->isoFormat('ddd, D MMM') }}</span>
        </div>

        @forelse($jadwalHariIni as $jadwal)
            <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
                    <i class="fas fa-book text-blue-500 text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $jadwal->mataKuliah->nama ?? '–' }}</p>
                    <p class="text-xs text-slate-400">
                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} –
                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                    </p>
                </div>
            </div>
        @empty
            <div class="py-6 flex flex-col items-center text-slate-400">
                <i class="fas fa-calendar-times text-3xl text-slate-200 mb-2"></i>
                <p class="text-sm text-slate-500">Tidak ada jadwal hari ini</p>
            </div>
        @endforelse
    </div>

    {{-- ── Riwayat Terakhir ─────────────────────────────────────────────── --}}
    @if($riwayatTerakhir->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-extrabold text-slate-800">Absensi Terakhir</p>
            <a href="{{ route('mahasiswa.riwayat') }}" class="text-xs text-blue-600 font-semibold">Lihat semua</a>
        </div>

        @foreach($riwayatTerakhir as $abs)
            <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-slate-400 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-700 truncate max-w-[160px]">
                            {{ $abs->sesi->mataKuliah->nama ?? '–' }}
                        </p>
                        <p class="text-xs text-slate-400">
                            {{ $abs->waktu_scan ? $abs->waktu_scan->isoFormat('D MMM • HH:mm') : '–' }}
                        </p>
                    </div>
                </div>
                @if($abs->status === 'hadir')
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-600 rounded-full text-[11px] font-bold">Hadir</span>
                @elseif($abs->status === 'izin')
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-600 rounded-full text-[11px] font-bold">Izin</span>
                @else
                    <span class="px-2 py-0.5 bg-rose-100 text-rose-500 rounded-full text-[11px] font-bold">Alpha</span>
                @endif
            </div>
        @endforeach
    </div>
    @endif

    {{-- ── Aksi Cepat ───────────────────────────────────────────────────── --}}
    <div class="pb-2">
        <a href="{{ route('mahasiswa.scan') }}"
           class="block w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm text-center rounded-2xl shadow transition">
            <i class="fas fa-qrcode mr-2"></i> Scan QR Absensi
        </a>
    </div>

</div>
@endsection
