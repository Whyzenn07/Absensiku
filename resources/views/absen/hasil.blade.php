@extends('layouts.mobile')

@section('title', 'Hasil Absensi – AbsensiKu')

@section('content')
<div class="px-4 pt-4 flex flex-col items-center justify-center min-h-[70vh] space-y-6">

    @if($status === 'sukses')
        <div class="w-20 h-20 bg-emerald-100 rounded-3xl flex items-center justify-center">
            <i class="fas fa-check-circle text-emerald-500 text-4xl"></i>
        </div>
        <div class="text-center">
            <h1 class="text-xl font-extrabold text-slate-800">Absensi Berhasil!</h1>
            <p class="text-slate-500 text-sm mt-2">{{ $pesan }}</p>
        </div>

    @elseif($status === 'duplikat')
        <div class="w-20 h-20 bg-amber-100 rounded-3xl flex items-center justify-center">
            <i class="fas fa-exclamation-circle text-amber-500 text-4xl"></i>
        </div>
        <div class="text-center">
            <h1 class="text-xl font-extrabold text-slate-800">Sudah Tercatat</h1>
            <p class="text-slate-500 text-sm mt-2">{{ $pesan }}</p>
        </div>

    @elseif($status === 'salah_kelas')
        <div class="w-20 h-20 bg-rose-100 rounded-3xl flex items-center justify-center">
            <i class="fas fa-ban text-rose-500 text-4xl"></i>
        </div>
        <div class="text-center">
            <h1 class="text-xl font-extrabold text-slate-800">Kelas Tidak Sesuai</h1>
            <p class="text-slate-500 text-sm mt-2">{{ $pesan }}</p>
        </div>

    @else
        <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center">
            <i class="fas fa-lock text-slate-400 text-4xl"></i>
        </div>
        <div class="text-center">
            <h1 class="text-xl font-extrabold text-slate-800">Sesi Berakhir</h1>
            <p class="text-slate-500 text-sm mt-2">{{ $pesan }}</p>
        </div>
    @endif

    {{-- Sesi info --}}
    <div class="w-full bg-white rounded-2xl border border-slate-100 p-4 shadow-sm text-center">
        <p class="text-sm font-bold text-slate-700">{{ $sesi->mataKuliah->nama ?? '–' }}</p>
        <p class="text-xs text-slate-400 mt-0.5">{{ $sesi->kelas->nama ?? '–' }}</p>
        <p class="text-xs text-slate-400 mt-1">
            <i class="fas fa-clock mr-1"></i>
            {{ now()->isoFormat('ddd, D MMM YYYY • HH:mm') }}
        </p>
    </div>

    <a href="{{ route('mahasiswa.dashboard') }}"
       class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm text-center rounded-2xl transition shadow block">
        Kembali ke Dashboard
    </a>
</div>
@endsection
