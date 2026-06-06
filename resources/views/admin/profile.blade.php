@extends('layouts.mobile')

@section('title', 'Profil – AbsensiKu')

@section('content')
<div class="px-4 pt-4 pb-4 space-y-4">

    {{-- ── Profile Card ─────────────────────────────────────────────────── --}}
    @php
        $initials = collect(explode(' ', $user->name))->take(2)->map(fn($w) => strtoupper($w[0]))->join('');
    @endphp
    <div class="bg-blue-600 rounded-2xl p-5 shadow-md flex flex-col items-center text-center">
        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-white font-extrabold text-2xl mb-3">
            {{ $initials }}
        </div>
        <p class="text-white font-extrabold text-base">{{ $user->name }}</p>
        <p class="text-blue-200 text-sm mt-0.5">{{ $user->email }}</p>
        <span class="mt-2 px-3 py-1 bg-white/20 text-white rounded-full text-xs font-semibold">
            Admin / Dosen
        </span>
    </div>

    {{-- ── Info Card ────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm space-y-3">
        <p class="text-sm font-extrabold text-slate-800">Informasi Akun</p>

        <div class="flex items-center gap-3 py-2 border-b border-slate-100">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-user text-blue-500 text-sm"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400">Nama</p>
                <p class="text-sm font-semibold text-slate-700">{{ $user->name }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3 py-2">
            <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-envelope text-violet-500 text-sm"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400">Email</p>
                <p class="text-sm font-semibold text-slate-700">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    {{-- ── Menu Cepat ───────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <a href="{{ route('mahasiswa.index') }}"
           class="flex items-center gap-3 px-4 py-3.5 border-b border-slate-100 hover:bg-slate-50 transition">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-users text-blue-500 text-sm"></i>
            </div>
            <p class="text-sm font-semibold text-slate-700 flex-1">Manajemen Mahasiswa</p>
            <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
        </a>
        <a href="{{ route('admin.riwayat') }}"
           class="flex items-center gap-3 px-4 py-3.5 border-b border-slate-100 hover:bg-slate-50 transition">
            <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-clipboard-list text-emerald-500 text-sm"></i>
            </div>
            <p class="text-sm font-semibold text-slate-700 flex-1">Riwayat Absensi</p>
            <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
        </a>
        <a href="{{ route('admin.qr.create') }}"
           class="flex items-center gap-3 px-4 py-3.5 hover:bg-slate-50 transition">
            <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-qrcode text-amber-500 text-sm"></i>
            </div>
            <p class="text-sm font-semibold text-slate-700 flex-1">Buat QR Absensi</p>
            <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
        </a>
    </div>

    {{-- ── Ganti Password ───────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <p class="text-sm font-extrabold text-slate-800 mb-3">Ganti Password</p>
        <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-3">
            @csrf
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600">Password Lama</label>
                <input type="password" name="password_lama" required
                    class="w-full bg-slate-50 border rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('password_lama') ? 'border-red-400' : 'border-slate-200' }}"
                    placeholder="Password saat ini">
                @error('password_lama') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600">Password Baru</label>
                <input type="password" name="password" required
                    class="w-full bg-slate-50 border rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('password') ? 'border-red-400' : 'border-slate-200' }}"
                    placeholder="Min. 8 karakter">
                @error('password') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ulangi password baru">
            </div>
            <button type="submit"
                class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow">
                Perbarui Password
            </button>
        </form>
    </div>

    {{-- ── Logout ───────────────────────────────────────────────────────── --}}
    <form action="{{ route('logout') }}" method="POST" class="pb-2">
        @csrf
        <button type="submit"
            class="w-full py-3 border-2 border-red-400 text-red-500 hover:bg-red-50 font-bold text-sm rounded-2xl transition">
            <i class="fas fa-sign-out-alt mr-2"></i> Keluar
        </button>
    </form>

</div>
@endsection
