@extends('layouts.mobile')

@section('title', 'Manajemen Mahasiswa – AbsensiKu')

@section('content')
<div class="px-4 pt-4 space-y-4">

    {{-- ── Page Header (Blue) ─────────────────────────────────────────── --}}
    <div class="bg-blue-600 -mx-4 -mt-4 px-4 pt-4 pb-3 mb-1 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}"
               class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center text-white hover:bg-white/30 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-base font-extrabold text-white">Manajemen Mahasiswa</h1>
                <p class="text-xs text-blue-200">{{ $mahasiswas->total() }} Mahasiswa Terdaftar</p>
            </div>
        </div>
        <a href="{{ route('mahasiswa.create') }}"
           class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center text-white hover:bg-white/30 transition">
            <i class="fas fa-plus text-sm"></i>
        </a>
    </div>

    {{-- ── Search Bar ───────────────────────────────────────────────────── --}}
    <form action="{{ route('mahasiswa.index') }}" method="GET" id="filter-form">
        <div class="relative">
            <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-search text-sm"></i>
            </span>
            <input type="text" name="search" value="{{ $search }}"
                class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-400"
                placeholder="Cari nama, NIM, atau kelas..."
                oninput="debounceSubmit()">
            @if($kelasId)
                <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
            @endif
        </div>
    </form>

    {{-- ── Filter Tabs (Kelas) ──────────────────────────────────────────── --}}
    <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1 scrollbar-hide">
        <a href="{{ route('mahasiswa.index', array_filter(['search' => $search])) }}"
           class="shrink-0 px-4 py-1.5 rounded-full text-xs font-bold border transition
                  {{ !$kelasId ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300' }}">
            Semua
        </a>
        @foreach($kelasList as $k)
            <a href="{{ route('mahasiswa.index', array_filter(['search' => $search, 'kelas_id' => $k->id])) }}"
               class="shrink-0 px-4 py-1.5 rounded-full text-xs font-bold border transition
                      {{ $kelasId == $k->id ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300' }}">
                {{ $k->nama }}
            </a>
        @endforeach
    </div>

    {{-- ── List Mahasiswa ───────────────────────────────────────────────── --}}
    @forelse($mahasiswas as $mhs)
        @php
            $colors = ['bg-blue-500','bg-violet-500','bg-emerald-500','bg-amber-500','bg-rose-500','bg-cyan-500'];
            $color  = $colors[crc32($mhs->user->name) % count($colors)];
            $initials = collect(explode(' ', $mhs->user->name))->take(2)->map(fn($w) => strtoupper($w[0]))->join('');
        @endphp

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <div class="flex items-start gap-3">
                {{-- Avatar --}}
                <div class="w-11 h-11 {{ $color }} rounded-xl flex items-center justify-center text-white font-extrabold text-sm shrink-0 shadow-sm">
                    {{ $initials }}
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 text-sm truncate">{{ $mhs->user->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $mhs->nim }} &bull; {{ $mhs->kelas->nama ?? '–' }}
                            </p>
                        </div>
                        {{-- Aksi --}}
                        <div class="flex items-center gap-1 shrink-0">
                            <a href="{{ route('mahasiswa.edit', $mhs->id) }}"
                               class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            <button type="button" onclick="konfirmasiHapus({{ $mhs->id }}, '{{ addslashes($mhs->user->name) }}')"
                               class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mt-2 flex flex-wrap items-center gap-1.5">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-[11px] font-semibold">
                            {{ $mhs->prodi->nama ?? '–' }}
                        </span>
                        <span class="text-[11px] text-slate-400">
                            <i class="fas fa-envelope mr-1"></i>{{ $mhs->user->email }}
                        </span>
                    </div>
                    @if($mhs->no_hp)
                        <p class="text-[11px] text-slate-400 mt-1">
                            <i class="fas fa-phone mr-1"></i>{{ $mhs->no_hp }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Hidden delete form --}}
        <form id="delete-form-{{ $mhs->id }}"
              action="{{ route('mahasiswa.destroy', $mhs->id) }}"
              method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @empty
        <div class="bg-white rounded-2xl border border-slate-100 py-16 flex flex-col items-center text-slate-400">
            <i class="fas fa-user-slash text-4xl text-slate-200 mb-3"></i>
            <p class="font-semibold text-slate-500">Belum ada mahasiswa</p>
            <p class="text-sm mt-1">{{ $search ? 'Coba kata kunci lain' : 'Tambah mahasiswa baru' }}</p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($mahasiswas->hasPages())
        <div class="pb-2">
            {{ $mahasiswas->links() }}
        </div>
    @endif

</div>

{{-- ── Modal Hapus ─────────────────────────────────────────────────────── --}}
<div id="modal-hapus" class="hidden fixed inset-0 z-50 items-end justify-center bg-black/40 px-4 pb-6">
    <div class="bg-white rounded-2xl w-full max-w-sm p-6 space-y-4 shadow-xl">
        <div class="flex flex-col items-center text-center gap-2">
            <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center">
                <i class="fas fa-trash-alt text-red-500 text-xl"></i>
            </div>
            <h2 class="font-extrabold text-slate-800 text-lg">Hapus Mahasiswa</h2>
            <p class="text-sm text-slate-500">
                Apakah Anda yakin ingin menghapus data
                <span id="modal-nama" class="font-bold text-slate-700"></span>?
                Tindakan ini tidak dapat dibalikkan.
            </p>
        </div>
        <div class="flex gap-3">
            <button onclick="tutupModal()"
                class="flex-1 py-3 rounded-xl border border-slate-200 text-slate-700 font-bold text-sm hover:bg-slate-50 transition">
                Batal
            </button>
            <button onclick="submitHapus()"
                class="flex-1 py-3 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold text-sm transition">
                Hapus
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let hapusId = null;

    function konfirmasiHapus(id, nama) {
        hapusId = id;
        document.getElementById('modal-nama').textContent = nama;
        const m = document.getElementById('modal-hapus');
        m.classList.remove('hidden');
        m.classList.add('flex');
    }

    function tutupModal() {
        const m = document.getElementById('modal-hapus');
        m.classList.add('hidden');
        m.classList.remove('flex');
        hapusId = null;
    }

    function submitHapus() {
        if (hapusId) document.getElementById('delete-form-' + hapusId).submit();
    }

    // Tutup modal saat klik backdrop
    document.getElementById('modal-hapus').addEventListener('click', function(e) {
        if (e.target === this) tutupModal();
    });

    // Debounce search
    let searchTimer;
    function debounceSubmit() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => document.getElementById('filter-form').submit(), 500);
    }
</script>
@endpush
