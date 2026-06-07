@extends('layouts.mobile')

@section('title', 'Profil – AbsensiKu')

@section('content')

{{-- ── Blue Header Section (full-width) ───────────────────────────────────── --}}
<div class="bg-blue-600 px-4 pt-4 pb-12 shadow-md">
    {{-- Sub-page back nav --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.dashboard') }}"
           class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center text-white hover:bg-white/30 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <p class="text-white font-extrabold text-base">Profile Saya</p>
    </div>

    {{-- Avatar + Name + Badge + NIP --}}
    @php
        $initials = collect(explode(' ', $user->name))->take(2)->map(fn($w) => strtoupper($w[0]))->join('');
        $nip = str_pad($user->id, 9, '0', STR_PAD_LEFT);
    @endphp

    <div class="flex flex-col items-center text-center">
        <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mb-3 shadow-inner">
            <i class="fas fa-user text-white text-4xl"></i>
        </div>
        <p class="text-white font-extrabold text-lg">{{ $user->name }}</p>
        <span class="mt-1.5 px-3 py-0.5 bg-white/20 text-white rounded-full text-xs font-bold">Admin / Dosen</span>
        <p class="text-blue-200 text-xs mt-1.5">NIP: {{ $nip }}</p>
    </div>
</div>

{{-- ── Cards (overlap blue section) ───────────────────────────────────────── --}}
<div class="px-4 space-y-4 -mt-6 pb-6">

    {{-- Informasi Pribadi --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 space-y-1">
        <p class="text-sm font-extrabold text-slate-800 mb-3">Informasi Pribadi</p>

        <div class="flex items-center gap-3 py-2.5 border-b border-slate-50">
            <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-user text-blue-500 text-sm"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-slate-400">Nama Lengkap</p>
                <p class="text-sm font-semibold text-slate-700">{{ $user->name }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3 py-2.5 border-b border-slate-50">
            <div class="w-9 h-9 bg-violet-50 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-bookmark text-violet-500 text-sm"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-slate-400">NIP</p>
                <p class="text-sm font-semibold text-slate-700">{{ $nip }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3 py-2.5 border-b border-slate-50">
            <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-envelope text-blue-500 text-sm"></i>
            </div>
            <div class="flex-1 min-w-0 overflow-hidden">
                <p class="text-xs text-slate-400">Email</p>
                <p class="text-sm font-semibold text-slate-700 truncate">{{ $user->email }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3 py-2.5">
            <div class="w-9 h-9 bg-emerald-50 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-phone text-emerald-500 text-sm"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-slate-400">No. Hp</p>
                <p class="text-sm font-semibold text-slate-400">–</p>
            </div>
        </div>
    </div>

    {{-- Pengaturan --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <p class="px-4 pt-3 pb-1 text-sm font-extrabold text-slate-800">Pengaturan</p>

        <button onclick="toggleGantiPassword()"
            class="w-full flex items-center gap-3 px-4 py-3.5 border-t border-slate-100 hover:bg-slate-50 transition">
            <div class="w-9 h-9 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-cog text-slate-500 text-sm"></i>
            </div>
            <p class="text-sm font-semibold text-slate-700 flex-1 text-left">Pengaturan Akun</p>
            <i id="pw-chevron" class="fas fa-chevron-right text-slate-300 text-xs transition-transform"></i>
        </button>

        <div class="flex items-center gap-3 px-4 py-3.5 border-t border-slate-100">
            <div class="w-9 h-9 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-bell text-slate-500 text-sm"></i>
            </div>
            <p class="text-sm font-semibold text-slate-700 flex-1">Notifikasi</p>
            <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
        </div>

        <div class="flex items-center gap-3 px-4 py-3.5 border-t border-slate-100">
            <div class="w-9 h-9 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-shield-alt text-slate-500 text-sm"></i>
            </div>
            <p class="text-sm font-semibold text-slate-700 flex-1">Keamanan</p>
            <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
        </div>

        <div class="flex items-center gap-3 px-4 py-3.5 border-t border-slate-100">
            <div class="w-9 h-9 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-question-circle text-slate-500 text-sm"></i>
            </div>
            <p class="text-sm font-semibold text-slate-700 flex-1">Bantuan & FAQ</p>
            <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
        </div>
    </div>

    {{-- Ganti Password (collapsible) --}}
    <div id="ganti-password-section" class="hidden bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
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

    {{-- Logout --}}
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
            class="w-full py-3 border-2 border-red-400 text-red-500 hover:bg-red-50 font-bold text-sm rounded-2xl transition flex items-center justify-center gap-2">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </button>
    </form>

</div>

@endsection

@push('scripts')
<script>
    function toggleGantiPassword() {
        const section = document.getElementById('ganti-password-section');
        const chevron = document.getElementById('pw-chevron');
        const isHidden = section.classList.contains('hidden');
        section.classList.toggle('hidden', !isHidden);
        chevron.style.transform = isHidden ? 'rotate(90deg)' : '';
    }

    @if($errors->has('password_lama') || $errors->has('password'))
        document.addEventListener('DOMContentLoaded', () => toggleGantiPassword());
    @endif
</script>
@endpush
