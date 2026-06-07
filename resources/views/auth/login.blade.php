@extends('layouts.auth')

@section('title', 'Masuk – AbsensiKu')

@section('content')
<div class="bg-white rounded-3xl shadow-lg p-8 space-y-6">

    {{-- Icon + Heading --}}
    <div class="flex flex-col items-center text-center space-y-3">
        <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center shadow">
            <i class="fas fa-qrcode text-white text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Selamat Datang!</h1>
            <p class="text-sm text-gray-500 mt-1">Silakan login untuk mengakses AbsensiKu</p>
        </div>
    </div>

    {{-- Alert --}}
    @if (session('status'))
        <div class="rounded-xl bg-blue-50 border border-blue-100 px-4 py-3 text-sm text-blue-700 font-medium">
            {{ session('status') }}
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div class="space-y-1">
            <label class="text-sm font-semibold text-gray-700">Alamat Email</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                    <i class="fas fa-envelope text-sm"></i>
                </span>
                <input name="email" type="email" required autocomplete="email"
                    class="input-field {{ $errors->has('email') ? 'error' : '' }}"
                    placeholder="nama@email.com" value="{{ old('email') }}">
            </div>
            @error('email')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="space-y-1">
            <label class="text-sm font-semibold text-gray-700">Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                    <i class="fas fa-lock text-sm"></i>
                </span>
                <input name="password" type="password" required autocomplete="current-password"
                    class="input-field {{ $errors->has('password') ? 'error' : '' }}"
                    placeholder="••••••••">
            </div>
            @error('password')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Lupa Password --}}
        <div class="flex justify-end">
            <a href="{{ route('password.request') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                Lupa Password?
            </a>
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="btn-primary w-full py-3 rounded-2xl text-white font-bold text-sm flex items-center justify-center gap-2 shadow">
            <i class="fas fa-sign-in-alt"></i>
            Masuk Sekarang
        </button>
    </form>

    {{-- Register link --}}
    <p class="text-center text-sm text-gray-600">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:text-blue-700 underline underline-offset-2">
            Daftar Akun Mahasiswa
        </a>
    </p>
</div>
@endsection
