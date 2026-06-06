@extends('layouts.auth')

@section('title', 'Buat Password Baru – AbsensiKu')

@section('content')
<div class="bg-white rounded-3xl shadow-lg p-8 space-y-6">

    {{-- Icon + Heading --}}
    <div class="flex flex-col items-center text-center space-y-3">
        <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center shadow">
            <i class="fas fa-lock text-white text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Reset Password</h1>
            <p class="text-sm text-gray-500 mt-1">Masukkan email dan password baru untuk mengganti password.</p>
        </div>
    </div>

    <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Email --}}
        <div class="space-y-1">
            <label class="text-sm font-semibold text-gray-700">Alamat Email</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                    <i class="fas fa-envelope text-sm"></i>
                </span>
                <input name="email" type="email" required autocomplete="email"
                    class="input-field {{ $errors->has('email') ? 'error' : '' }}"
                    placeholder="nama@email.com" value="{{ old('email', $email ?? '') }}">
            </div>
            @error('email')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password Baru --}}
        <div class="space-y-1">
            <label class="text-sm font-semibold text-gray-700">Password Baru</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                    <i class="fas fa-lock text-sm"></i>
                </span>
                <input name="password" type="password" required autocomplete="new-password"
                    class="input-field {{ $errors->has('password') ? 'error' : '' }}"
                    placeholder="Minimal 8 karakter">
            </div>
            @error('password')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="space-y-1">
            <label class="text-sm font-semibold text-gray-700">Konfirmasi Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                    <i class="fas fa-lock text-sm"></i>
                </span>
                <input name="password_confirmation" type="password" required autocomplete="new-password"
                    class="input-field"
                    placeholder="Ulangi Password Baru">
            </div>
        </div>

        <button type="submit"
            class="btn-primary w-full py-3 rounded-2xl text-white font-bold text-sm flex items-center justify-center gap-2 shadow">
            <i class="fas fa-shield-alt"></i>
            Simpan Password Baru
        </button>
    </form>

    <p class="text-center">
        <a href="{{ route('login') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 underline underline-offset-2">
            Kembali ke halaman login
        </a>
    </p>
</div>
@endsection
