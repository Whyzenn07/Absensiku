@extends('layouts.mobile')

@section('title', 'Hasil QR – AbsensiKu')

@push('styles')
<style>
    #qr-canvas { border-radius: 1rem; }
</style>
@endpush

@section('content')
<div class="px-4 pt-4 space-y-4">

    {{-- ── Page Header (Blue) ─────────────────────────────────────────── --}}
    <div class="bg-blue-600 -mx-4 -mt-4 px-4 pt-4 pb-3 mb-1">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}"
               class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center text-white hover:bg-white/30 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-base font-extrabold text-white">Generate QR Absensi</h1>
                <p class="text-xs text-blue-200">Buat QR Code untuk sesi kelas</p>
            </div>
        </div>
    </div>

    {{-- ── Info Badge + Countdown ───────────────────────────────────────── --}}
    <div class="bg-blue-600 rounded-2xl px-4 py-3 flex items-center justify-between shadow">
        <div>
            <p class="text-white font-bold text-sm">{{ $sesi->mataKuliah->nama }}</p>
            <p class="text-blue-200 text-xs mt-0.5">{{ $sesi->kelas->nama }}</p>
        </div>
        <div id="countdown-box"
             class="bg-white/20 rounded-xl px-3 py-1.5 text-center min-w-[64px]">
            <p id="countdown" class="text-white font-extrabold text-lg leading-none tracking-tight">
                {{ gmdate('i:s', $sisaDetik) }}
            </p>
            <p class="text-blue-200 text-[10px] mt-0.5">sisa waktu</p>
        </div>
    </div>

    {{-- ── QR Code Card ─────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex flex-col items-center space-y-4">

        {{-- Expired overlay --}}
        <div id="expired-overlay" class="{{ $sesi->status === 'selesai' ? '' : 'hidden' }}
            w-full flex flex-col items-center py-8 text-slate-400">
            <i class="fas fa-lock text-5xl text-slate-300 mb-3"></i>
            <p class="font-semibold text-slate-500">Sesi Telah Berakhir</p>
            <p class="text-xs mt-1">QR Code sudah tidak aktif</p>
        </div>

        {{-- QR container --}}
        <div id="qr-container" class="{{ $sesi->status === 'selesai' ? 'hidden' : '' }} flex flex-col items-center space-y-3 w-full">
            <div id="qr-code" class="p-3 bg-white border-2 border-blue-100 rounded-2xl shadow-inner"></div>
            <p class="text-xs text-slate-500 text-center">
                Tampilkan QR ini kepada mahasiswa
            </p>
            <p class="text-[11px] text-slate-400 text-center">
                QR Code diperbarui setiap saat setelah diaktifkan
            </p>
        </div>

        {{-- Token Absensi --}}
        <div class="w-full space-y-1">
            <p class="text-xs font-semibold text-slate-500">Token Absensi</p>
            <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                <span id="token-text" class="flex-1 font-mono font-bold text-slate-800 tracking-widest text-lg">
                    {{ $sesi->token }}
                </span>
                <button onclick="salinToken()"
                    class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition">
                    <i class="fas fa-copy text-sm" id="copy-icon"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Rekap Kehadiran Real-time ─────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
        <p class="text-sm font-bold text-slate-700 mb-3">Rekap Kehadiran Saat Ini</p>
        <div class="grid grid-cols-3 gap-3">
            <div class="flex flex-col items-center bg-emerald-50 rounded-xl py-3 border border-emerald-100">
                <span id="stat-hadir" class="text-2xl font-extrabold text-emerald-600">{{ $hadir }}</span>
                <span class="text-xs text-emerald-500 font-semibold mt-0.5">Hadir</span>
            </div>
            <div class="flex flex-col items-center bg-rose-50 rounded-xl py-3 border border-rose-100">
                <span id="stat-tidak" class="text-2xl font-extrabold text-rose-500">{{ $tidak }}</span>
                <span class="text-xs text-rose-400 font-semibold mt-0.5">Belum</span>
            </div>
            <div class="flex flex-col items-center bg-blue-50 rounded-xl py-3 border border-blue-100">
                <span id="stat-total" class="text-2xl font-extrabold text-blue-600">{{ $totalKelas }}</span>
                <span class="text-xs text-blue-400 font-semibold mt-0.5">Total</span>
            </div>
        </div>
    </div>

    {{-- ── Action Buttons ───────────────────────────────────────────────── --}}
    <div class="flex gap-3 pb-4">
        <form action="{{ route('admin.qr.sesi-baru', $sesi) }}" method="POST" class="flex-1">
            @csrf
            <button type="submit"
                class="w-full py-3 rounded-xl border-2 border-blue-600 text-blue-600 font-bold text-sm hover:bg-blue-50 transition">
                Sesi Baru
            </button>
        </form>
        <form action="{{ route('admin.qr.selesai', $sesi) }}" method="POST" class="flex-1">
            @csrf
            <button type="submit"
                class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm transition shadow">
                Selesai
            </button>
        </form>
    </div>

</div>
@endsection

@push('scripts')
{{-- QR Code generator (client-side, no server dependency) --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    const QR_DATA     = @json($sesi->qr_data);
    const SESI_ID     = @json($sesi->id);
    const REFRESH_URL = @json(route('admin.qr.refresh', $sesi));
    const IS_AKTIF    = @json($sesi->status === 'aktif');

    // ── Generate QR ────────────────────────────────────────────────────
    if (IS_AKTIF) {
        new QRCode(document.getElementById('qr-code'), {
            text:           QR_DATA,
            width:          220,
            height:         220,
            colorDark:      '#1e293b',
            colorLight:     '#ffffff',
            correctLevel:   QRCode.CorrectLevel.H,
        });
    }

    // ── Countdown Timer ─────────────────────────────────────────────────
    let sisaDetik = @json((int) $sisaDetik);

    function formatTimer(s) {
        const m = Math.floor(s / 60).toString().padStart(2, '0');
        const d = (s % 60).toString().padStart(2, '0');
        return m + ':' + d;
    }

    const countdownEl = document.getElementById('countdown');
    const countdownBox = document.getElementById('countdown-box');

    let timerInterval = null;

    function tickCountdown() {
        if (sisaDetik <= 0) {
            clearInterval(timerInterval);
            countdownEl.textContent = '00:00';
            countdownBox.classList.replace('bg-white/20', 'bg-red-500/40');
            return;
        }
        sisaDetik--;
        countdownEl.textContent = formatTimer(sisaDetik);
        if (sisaDetik <= 60) {
            countdownBox.classList.add('bg-red-500/40');
            countdownBox.classList.remove('bg-white/20');
        }
    }

    if (IS_AKTIF && sisaDetik > 0) {
        timerInterval = setInterval(tickCountdown, 1000);
    }

    // ── Real-time polling (setiap 5 detik) ─────────────────────────────
    function refreshStats() {
        fetch(REFRESH_URL, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                document.getElementById('stat-hadir').textContent = data.hadir;
                document.getElementById('stat-tidak').textContent = data.tidak;
                document.getElementById('stat-total').textContent = data.total;
                sisaDetik = data.sisa_detik;

                if (data.status === 'selesai') {
                    clearInterval(timerInterval);
                    clearInterval(pollInterval);
                    document.getElementById('qr-container').classList.add('hidden');
                    document.getElementById('expired-overlay').classList.remove('hidden');
                    countdownEl.textContent = '00:00';
                }
            })
            .catch(() => {});
    }

    let pollInterval = null;
    if (IS_AKTIF) {
        pollInterval = setInterval(refreshStats, 5000);
    }

    // ── Salin Token ─────────────────────────────────────────────────────
    function salinToken() {
        const token = document.getElementById('token-text').textContent.trim();
        navigator.clipboard.writeText(token).then(() => {
            const icon = document.getElementById('copy-icon');
            icon.className = 'fas fa-check text-sm text-emerald-600';
            setTimeout(() => { icon.className = 'fas fa-copy text-sm'; }, 2000);
        });
    }
</script>
@endpush
