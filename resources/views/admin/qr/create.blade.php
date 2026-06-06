@extends('layouts.mobile')

@section('title', 'Generate QR – AbsensiKu')

@section('content')
<div class="px-4 pt-4 space-y-4">

    {{-- ── Page Header ──────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}"
           class="w-9 h-9 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-base font-extrabold text-slate-800">Generate QR Absensi</h1>
            <p class="text-xs text-slate-500">Buat QR Code untuk sesi hari ini</p>
        </div>
    </div>

    {{-- ── Form Card ────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 space-y-4">
        <h2 class="text-sm font-bold text-slate-700 border-b border-slate-100 pb-2">Detail Absensi</h2>

        <form action="{{ route('admin.qr.store') }}" method="POST" id="qr-form" class="space-y-4">
            @csrf

            {{-- Mata Kuliah --}}
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600">Mata Kuliah</label>
                <div class="relative">
                    <select name="mata_kuliah_id" id="sel-mk" required
                        class="w-full appearance-none bg-slate-50 border rounded-xl px-4 py-3 pr-10 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('mata_kuliah_id') ? 'border-red-400' : 'border-slate-200' }}">
                        <option value="" disabled selected>Pilih Mata Kuliah</option>
                        @foreach($mataKuliahs as $mk)
                            <option value="{{ $mk->id }}" {{ old('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                                {{ $mk->nama }}
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </span>
                </div>
                @error('mata_kuliah_id') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Kelas --}}
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600">Kelas</label>
                <div class="relative">
                    <select name="kelas_id" id="sel-kelas" required
                        class="w-full appearance-none bg-slate-50 border rounded-xl px-4 py-3 pr-10 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('kelas_id') ? 'border-red-400' : 'border-slate-200' }}">
                        <option value="" disabled selected>Pilih Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
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

            {{-- Durasi --}}
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600">Durasi QR Code (Menit)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-clock text-sm"></i>
                    </span>
                    <input type="number" name="durasi" id="durasi" min="1" max="300" required
                        value="{{ old('durasi', 90) }}"
                        class="w-full bg-slate-50 border rounded-xl pl-10 pr-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('durasi') ? 'border-red-400' : 'border-slate-200' }}"
                        placeholder="90">
                </div>
                @error('durasi') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl text-sm flex items-center justify-center gap-2 transition shadow">
                <i class="fas fa-qrcode text-base"></i>
                Generate QR Code
            </button>
        </form>
    </div>

    {{-- ── List Mata Kuliah ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-700 mb-3">List Mata Kuliah</h3>
        <div class="space-y-1">
            @forelse($mataKuliahs as $mk)
                <button type="button" onclick="pilihMk({{ $mk->id }})"
                    class="mk-chip w-full text-left px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition font-medium"
                    data-id="{{ $mk->id }}">
                    {{ $mk->nama }}
                </button>
            @empty
                <p class="text-xs text-slate-400 py-2">Belum ada mata kuliah.</p>
            @endforelse
        </div>
    </div>

    {{-- ── List Kelas ───────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-700 mb-3">List Kelas</h3>
        <div class="grid grid-cols-3 gap-2">
            @foreach($kelas as $k)
                <button type="button" onclick="pilihKelas({{ $k->id }})"
                    class="kelas-chip px-3 py-2 rounded-lg text-sm text-center text-slate-700 bg-slate-50 border border-slate-200 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition font-medium"
                    data-id="{{ $k->id }}">
                    {{ $k->nama }}
                </button>
            @endforeach
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function pilihMk(id) {
        document.getElementById('sel-mk').value = id;
        document.querySelectorAll('.mk-chip').forEach(el => {
            el.classList.toggle('bg-blue-50', el.dataset.id == id);
            el.classList.toggle('text-blue-700', el.dataset.id == id);
            el.classList.toggle('font-bold', el.dataset.id == id);
        });
    }

    function pilihKelas(id) {
        document.getElementById('sel-kelas').value = id;
        document.querySelectorAll('.kelas-chip').forEach(el => {
            el.classList.toggle('bg-blue-600', el.dataset.id == id);
            el.classList.toggle('text-white', el.dataset.id == id);
            el.classList.toggle('border-blue-600', el.dataset.id == id);
        });
    }

    // Sinkronisasi chip aktif dengan dropdown (old value)
    const oldMk    = document.getElementById('sel-mk').value;
    const oldKelas = document.getElementById('sel-kelas').value;
    if (oldMk)    pilihMk(oldMk);
    if (oldKelas) pilihKelas(oldKelas);
</script>
@endpush
