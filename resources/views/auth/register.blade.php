@extends('layouts.auth')

@section('title', 'Daftar – AbsensiKu')

@section('content')
@php
    $initialStep = ($errors->has('nim') || $errors->has('no_hp') || $errors->has('prodi_id') || $errors->has('kelas_id')) ? 2 : 1;
@endphp

<div class="bg-white rounded-3xl shadow-lg p-8 space-y-6">

    {{-- Icon + Heading --}}
    <div class="flex flex-col items-center text-center space-y-3">
        <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center shadow">
            <i class="fas fa-user-plus text-white text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Buat Akun Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Mulai kelola absensi Anda dengan AbsensiKu</p>
        </div>
    </div>

    <form id="register-form" data-initial-step="{{ $initialStep }}"
          action="{{ route('register') }}" method="POST" class="space-y-4">
        @csrf

        {{-- ── STEP 1: Informasi Akun ── --}}
        <div id="step-1" class="space-y-4">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-700">Tahap 1: Informasi Akun</p>
                <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-100 px-3 py-1 rounded-full">1 dari 2</span>
            </div>

            {{-- Nama --}}
            <div class="space-y-1">
                <label class="text-sm font-semibold text-gray-700">Nama Lengkap</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                        <i class="fas fa-user text-sm"></i>
                    </span>
                    <input name="name" type="text" required autocomplete="name"
                        class="input-field {{ $errors->has('name') ? 'error' : '' }}"
                        placeholder="John Doe" value="{{ old('name') }}">
                </div>
                @error('name') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

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
                @error('email') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div class="space-y-1">
                <label class="text-sm font-semibold text-gray-700">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                        <i class="fas fa-lock text-sm"></i>
                    </span>
                    <input name="password" type="password" required autocomplete="new-password"
                        class="input-field {{ $errors->has('password') ? 'error' : '' }}"
                        placeholder="••••••••">
                </div>
                @error('password') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div class="space-y-1">
                <label class="text-sm font-semibold text-gray-700">Konfirmasi Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                        <i class="fas fa-lock text-sm"></i>
                    </span>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                        class="input-field"
                        placeholder="••••••••">
                </div>
            </div>

            <button id="btn-next" type="button"
                class="btn-primary w-full py-3 rounded-2xl text-white font-bold text-sm flex items-center justify-center gap-2 shadow">
                Lanjutkan
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>

        {{-- ── STEP 2: Informasi Akademik ── --}}
        <div id="step-2" class="space-y-4 hidden">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-700">Tahap 2: Informasi Akademik</p>
                <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-100 px-3 py-1 rounded-full">2 dari 2</span>
            </div>

            {{-- NIM --}}
            <div class="space-y-1">
                <label class="text-sm font-semibold text-gray-700">NIM</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                        <i class="fas fa-id-card text-sm"></i>
                    </span>
                    <input name="nim" type="text" required autocomplete="off"
                        class="input-field {{ $errors->has('nim') ? 'error' : '' }}"
                        placeholder="2021XXXXXXXX" value="{{ old('nim') }}">
                </div>
                @error('nim') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- No HP --}}
            <div class="space-y-1">
                <label class="text-sm font-semibold text-gray-700">No HP</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                        <i class="fas fa-phone text-sm"></i>
                    </span>
                    <input name="no_hp" type="text" autocomplete="tel"
                        class="input-field"
                        placeholder="08XXXXXXXXXX" value="{{ old('no_hp') }}">
                </div>
            </div>

            {{-- Program Studi --}}
            <div class="space-y-1">
                <label class="text-sm font-semibold text-gray-700">Program Studi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                        <i class="fas fa-graduation-cap text-sm"></i>
                    </span>
                    <select name="prodi_id" required
                        class="input-field {{ $errors->has('prodi_id') ? 'error' : '' }}">
                        <option value="" disabled {{ old('prodi_id') ? '' : 'selected' }}>Pilih</option>
                        @foreach ($prodis as $prodi)
                            <option value="{{ $prodi->id }}" {{ old('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama }}
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 pointer-events-none">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </span>
                </div>
                @error('prodi_id') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Kelas --}}
            <div class="space-y-1">
                <label class="text-sm font-semibold text-gray-700">Kelas</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                        <i class="fas fa-chalkboard text-sm"></i>
                    </span>
                    <select name="kelas_id" required
                        class="input-field {{ $errors->has('kelas_id') ? 'error' : '' }}">
                        <option value="" disabled {{ old('kelas_id') ? '' : 'selected' }}>Pilih</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 pointer-events-none">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </span>
                </div>
                @error('kelas_id') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-1">
                <button id="btn-back" type="button"
                    class="flex-1 py-3 rounded-2xl bg-white border border-gray-200 text-gray-700 font-bold text-sm hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </button>
                <button type="submit"
                    class="btn-primary flex-1 py-3 rounded-2xl text-white font-bold text-sm shadow flex items-center justify-center gap-2">
                    <i class="fas fa-user-check"></i>
                    Lanjutkan
                </button>
            </div>
        </div>
    </form>

    <p class="text-center text-sm text-gray-600">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-700 underline underline-offset-2">
            Login di sini
        </a>
    </p>
</div>
@endsection

@push('scripts')
<script>
    const step1 = document.getElementById('step-1');
    const step2 = document.getElementById('step-2');
    const form  = document.getElementById('register-form');

    function showStep(n) {
        step1.classList.toggle('hidden', n !== 1);
        step2.classList.toggle('hidden', n !== 2);
    }

    document.getElementById('btn-next').addEventListener('click', () => {
        const name     = form.querySelector('[name=name]');
        const email    = form.querySelector('[name=email]');
        const password = form.querySelector('[name=password]');
        const confirm  = form.querySelector('[name=password_confirmation]');

        confirm.setCustomValidity('');
        for (const el of [name, email, password, confirm]) {
            if (!el.checkValidity()) { el.reportValidity(); return; }
        }
        if (password.value !== confirm.value) {
            confirm.setCustomValidity('Konfirmasi password tidak sama.');
            confirm.reportValidity();
            return;
        }
        showStep(2);
    });

    document.getElementById('btn-back').addEventListener('click', () => showStep(1));

    showStep(Number(form.dataset.initialStep || '1'));
</script>
@endpush
