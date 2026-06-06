@extends('layouts.mobile')

@section('title', 'Edit Mahasiswa – AbsensiKu')

@section('content')
<div class="px-4 pt-4 pb-4 space-y-4">

    {{-- ── Page Header ──────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('mahasiswa.index') }}"
           class="w-9 h-9 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-base font-extrabold text-slate-800">Edit Mahasiswa</h1>
            <p class="text-xs text-slate-500">{{ $mahasiswa->nim }}</p>
        </div>
    </div>

    {{-- ── Form Card ────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <form method="POST" action="{{ route('mahasiswa.update', $mahasiswa->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Nama Lengkap --}}
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600">Nama Lengkap</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-user text-sm"></i>
                    </span>
                    <input type="text" name="name" value="{{ old('name', $mahasiswa->user->name) }}" required
                        class="w-full bg-slate-50 border rounded-xl pl-10 pr-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('name') ? 'border-red-400' : 'border-slate-200' }}"
                        placeholder="Nama Lengkap">
                </div>
                @error('name') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- NIM --}}
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600">NIM</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-id-card text-sm"></i>
                    </span>
                    <input type="text" name="nim" value="{{ old('nim', $mahasiswa->nim) }}" required
                        class="w-full bg-slate-50 border rounded-xl pl-10 pr-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('nim') ? 'border-red-400' : 'border-slate-200' }}"
                        placeholder="xxxxx">
                </div>
                @error('nim') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600">Alamat Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-envelope text-sm"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email', $mahasiswa->user->email) }}" required
                        class="w-full bg-slate-50 border rounded-xl pl-10 pr-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('email') ? 'border-red-400' : 'border-slate-200' }}"
                        placeholder="email@example.com">
                </div>
                @error('email') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- No HP --}}
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600">No HP</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-phone text-sm"></i>
                    </span>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $mahasiswa->no_hp) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="08xxxxxxxxxx">
                </div>
            </div>

            {{-- Program Studi --}}
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600">Program Studi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-graduation-cap text-sm"></i>
                    </span>
                    <select name="prodi_id" required
                        class="w-full appearance-none bg-slate-50 border rounded-xl pl-10 pr-8 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('prodi_id') ? 'border-red-400' : 'border-slate-200' }}">
                        @foreach($prodis as $p)
                            <option value="{{ $p->id }}" {{ old('prodi_id', $mahasiswa->prodi_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->nama }}
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </span>
                </div>
                @error('prodi_id') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Kelas --}}
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600">Kelas</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-chalkboard text-sm"></i>
                    </span>
                    <select name="kelas_id" required
                        class="w-full appearance-none bg-slate-50 border rounded-xl pl-10 pr-8 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('kelas_id') ? 'border-red-400' : 'border-slate-200' }}">
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id', $mahasiswa->kelas_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </span>
                </div>
                @error('kelas_id') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Password (opsional) --}}
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600">Ganti Password <span class="font-normal text-slate-400">(opsional)</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-lock text-sm"></i>
                    </span>
                    <input type="password" name="password"
                        class="w-full bg-slate-50 border rounded-xl pl-10 pr-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('password') ? 'border-red-400' : 'border-slate-200' }}"
                        placeholder="Biarkan kosong jika tidak diubah">
                </div>
                @error('password') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('mahasiswa.index') }}"
                   class="flex-1 py-3 rounded-xl border border-slate-200 text-slate-700 font-bold text-sm text-center hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm transition shadow">
                    Selesai
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
