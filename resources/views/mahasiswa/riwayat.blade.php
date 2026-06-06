@extends('layouts.mobile')

@section('title', 'Riwayat Absensi – AbsensiKu')

@section('content')
<div class="px-4 pt-4 space-y-4">

    {{-- ── Page Header ──────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('mahasiswa.dashboard') }}"
           class="w-9 h-9 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-base font-extrabold text-slate-800">Riwayat Absensi</h1>
            <p class="text-xs text-slate-500">{{ $absensis->total() }} catatan absensi</p>
        </div>
    </div>

    {{-- ── Absensi List ─────────────────────────────────────────────────── --}}
    @forelse($absensis as $abs)
        @php
            $statusColor = match($abs->status) {
                'hadir' => 'bg-emerald-100 text-emerald-600',
                'izin'  => 'bg-amber-100 text-amber-600',
                default => 'bg-rose-100 text-rose-500',
            };
            $statusLabel = match($abs->status) {
                'hadir' => 'Hadir',
                'izin'  => 'Izin',
                default => 'Alpha',
            };
            $iconColor = match($abs->status) {
                'hadir' => 'bg-emerald-50 text-emerald-500',
                'izin'  => 'bg-amber-50 text-amber-500',
                default => 'bg-rose-50 text-rose-400',
            };
        @endphp
        <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 {{ $iconColor }} rounded-xl flex items-center justify-center shrink-0">
                <i class="fas fa-{{ $abs->status === 'hadir' ? 'check-circle' : ($abs->status === 'izin' ? 'info-circle' : 'times-circle') }} text-sm"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-800 text-sm truncate">{{ $abs->sesi->mataKuliah->nama ?? '–' }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $abs->sesi->kelas->nama ?? '–' }}</p>
                <p class="text-xs text-slate-400">
                    <i class="fas fa-clock mr-1"></i>
                    {{ $abs->waktu_scan ? $abs->waktu_scan->isoFormat('ddd, D MMM YYYY • HH:mm') : '–' }}
                </p>
            </div>
            <span class="shrink-0 px-2.5 py-1 {{ $statusColor }} rounded-full text-[11px] font-bold">
                {{ $statusLabel }}
            </span>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-slate-100 py-16 flex flex-col items-center text-slate-400">
            <i class="fas fa-clipboard-list text-5xl text-slate-200 mb-3"></i>
            <p class="font-semibold text-slate-500">Belum ada riwayat absensi</p>
            <p class="text-sm mt-1">Scan QR Code dosen untuk mulai absensi</p>
        </div>
    @endforelse

    @if($absensis->hasPages())
        <div class="pb-2">{{ $absensis->links() }}</div>
    @endif

</div>
@endsection
