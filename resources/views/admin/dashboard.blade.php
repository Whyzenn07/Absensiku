@extends('layouts.mobile')

@section('title', 'Dashboard – AbsensiKu')

@section('content')
<div class="space-y-4 px-4 pt-4">

    {{-- ── Hero Card ────────────────────────────────────────────────────── --}}
    <div class="bg-blue-600 rounded-2xl p-5 text-white shadow">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-xs font-bold bg-white/20 px-2.5 py-1 rounded-full">Admin / Dosen</span>
        </div>
        <h2 class="text-xl font-extrabold leading-tight">{{ auth()->user()->name }}</h2>
        <p class="text-blue-200 text-xs mt-0.5">NIP: {{ str_pad(auth()->user()->id, 10, '0', STR_PAD_LEFT) }}</p>

        <div class="mt-4 flex items-center gap-2 bg-blue-500/50 rounded-xl px-3 py-2">
            <i class="fas fa-clock text-blue-200 text-sm"></i>
            <div>
                <p class="text-white font-semibold text-sm">{{ $tanggal }}</p>
                <p class="text-blue-200 text-xs">Panel Admin Absensi</p>
            </div>
        </div>
    </div>

    {{-- ── Stats Cards ──────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-3">
        {{-- Total Mahasiswa --}}
        <div class="stat-card shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-graduate text-blue-600 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-800">{{ $totalMahasiswa }}</p>
            <p class="text-xs text-slate-500 mt-0.5">Total Mahasiswa</p>
        </div>

        {{-- Total Sesi --}}
        <div class="stat-card shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list-check text-amber-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-800">{{ $totalSesiHariIni }}</p>
            <p class="text-xs text-slate-500 mt-0.5">Total Sesi</p>
        </div>

        {{-- % Kehadiran --}}
        <div class="stat-card shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-pie text-emerald-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-800">{{ $persentaseKehadiran }}%</p>
            <p class="text-xs text-slate-500 mt-0.5">Kehadiran</p>
        </div>

        {{-- Mata Kuliah --}}
        <div class="stat-card shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-purple-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-800">{{ $totalMataKuliah }}</p>
            <p class="text-xs text-slate-500 mt-0.5">Mata Kuliah</p>
        </div>
    </div>

    {{-- ── Aksi Cepat ───────────────────────────────────────────────────── --}}
    <div class="card p-4 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-700 mb-3">Aksi Cepat</h3>
        <div class="grid grid-cols-3 gap-2">
            <a href="{{ route('admin.qr.create') }}"
               class="flex flex-col items-center gap-2 p-3 bg-blue-600 rounded-xl text-white hover:bg-blue-700 transition">
                <i class="fas fa-qrcode text-xl"></i>
                <span class="text-[11px] font-semibold text-center leading-tight">Buat QR</span>
            </a>
            <a href="{{ route('mahasiswa.index') }}"
               class="flex flex-col items-center gap-2 p-3 bg-blue-50 rounded-xl text-blue-600 border border-blue-100 hover:bg-blue-100 transition">
                <i class="fas fa-users text-xl"></i>
                <span class="text-[11px] font-semibold text-center leading-tight">Mahasiswa</span>
            </a>
            <a href="{{ route('admin.riwayat') }}"
               class="flex flex-col items-center gap-2 p-3 bg-blue-50 rounded-xl text-blue-600 border border-blue-100 hover:bg-blue-100 transition">
                <i class="fas fa-history text-xl"></i>
                <span class="text-[11px] font-semibold text-center leading-tight">Riwayat</span>
            </a>
        </div>
    </div>

    {{-- ── Sesi Hari Ini ────────────────────────────────────────────────── --}}
    <div class="card p-4 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-slate-700">Sesi Hari Ini</h3>
            <a href="{{ route('admin.riwayat') }}" class="text-xs font-semibold text-blue-600 hover:underline">
                Lihat Semua
            </a>
        </div>

        @if($sesiHariIni->isEmpty())
            <div class="text-center py-8 text-slate-400">
                <i class="fas fa-calendar-times text-3xl mb-2 block"></i>
                <p class="text-sm">Belum ada sesi hari ini</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($sesiHariIni as $item)
                    @php $sesi = $item['sesi']; @endphp
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        {{-- Icon mata kuliah --}}
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fas fa-book-open text-blue-600 text-sm"></i>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">
                                {{ $sesi->mataKuliah->nama ?? '–' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $sesi->kelas->nama ?? '–' }} &bull;
                                {{ $sesi->started_at->format('d M Y H:i') }}
                            </p>
                        </div>

                        {{-- Hadir/Total --}}
                        <div class="shrink-0 text-right">
                            <span class="text-sm font-bold text-slate-700">
                                {{ $item['hadir'] }}/{{ $item['total_kelas'] }}
                            </span>
                            <p class="text-[10px] text-slate-400">hadir</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
