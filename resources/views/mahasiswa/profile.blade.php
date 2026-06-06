@extends('layouts.mobile')

@section('title', 'Profil – AbsensiKu')

@section('content')
<div class="px-4 pt-4 pb-4 space-y-4">

    {{-- ── Profile Card ─────────────────────────────────────────────────── --}}
    <div class="bg-blue-600 rounded-2xl p-5 shadow-md flex flex-col items-center text-center">
        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-white font-extrabold text-2xl mb-3">
            {{ $mahasiswa->inisials }}
        </div>
        <p class="text-white font-extrabold text-base">{{ $mahasiswa->user->name }}</p>
        <p class="text-blue-200 text-sm mt-0.5">{{ $mahasiswa->nim }}</p>
        <div class="flex items-center gap-2 mt-2">
            <span class="px-3 py-1 bg-white/20 text-white rounded-full text-xs font-semibold">
                {{ $mahasiswa->prodi->nama ?? '–' }}
            </span>
            <span class="px-3 py-1 bg-white/20 text-white rounded-full text-xs font-semibold">
                {{ $mahasiswa->kelas->nama ?? '–' }}
            </span>
        </div>
    </div>

    {{-- ── Info Card ────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm space-y-3">
        <p class="text-sm font-extrabold text-slate-800">Informasi Akun</p>

        <div class="flex items-center gap-3 py-2 border-b border-slate-100">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-envelope text-blue-500 text-sm"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400">Email</p>
                <p class="text-sm font-semibold text-slate-700">{{ $mahasiswa->user->email }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3 py-2 border-b border-slate-100">
            <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-phone text-emerald-500 text-sm"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400">No HP</p>
                <p class="text-sm font-semibold text-slate-700">{{ $mahasiswa->no_hp ?? '–' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3 py-2">
            <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-graduation-cap text-violet-500 text-sm"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400">Program Studi</p>
                <p class="text-sm font-semibold text-slate-700">{{ $mahasiswa->prodi->nama ?? '–' }}</p>
            </div>
        </div>
    </div>

    {{-- ── Ganti Password ───────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <p class="text-sm font-extrabold text-slate-800 mb-3">Ganti Password</p>
        <form action="{{ route('mahasiswa.profile.password') }}" method="POST" class="space-y-3">
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
