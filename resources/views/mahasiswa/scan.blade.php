@extends('layouts.mobile')

@section('title', 'Scan QR – AbsensiKu')

@push('styles')
<style>
    #reader { width: 100%; border-radius: 1rem; overflow: hidden; }
    #reader video { border-radius: 1rem; }
    #reader__scan_region { border-radius: 1rem; }
</style>
@endpush

@section('content')
<div class="px-4 pt-4 pb-4 space-y-4">

    {{-- ── Page Header ──────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('mahasiswa.dashboard') }}"
           class="w-9 h-9 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-base font-extrabold text-slate-800">Scan QR Absensi</h1>
            <p class="text-xs text-slate-500">Arahkan kamera ke QR Code dosen</p>
        </div>
    </div>

    {{-- ── Camera Scanner ───────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <div id="reader" class="mb-3"></div>
        <p id="scan-status" class="text-xs text-center text-slate-400">Menginisialisasi kamera...</p>
    </div>

    {{-- ── Divider ──────────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3">
        <div class="flex-1 h-px bg-slate-200"></div>
        <span class="text-xs text-slate-400 font-semibold">atau masukkan token manual</span>
        <div class="flex-1 h-px bg-slate-200"></div>
    </div>

    {{-- ── Manual Token Input ───────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
        <form action="{{ route('absen.manual') }}" method="POST" class="space-y-3">
            @csrf
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600">Token Absensi (8 karakter)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-key text-sm"></i>
                    </span>
                    <input type="text" name="token" maxlength="8" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm font-mono font-bold text-slate-800 tracking-widest uppercase focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="XXXXXXXX"
                        oninput="this.value = this.value.toUpperCase()">
                </div>
                @error('token') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <button type="submit"
                class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow">
                Konfirmasi Kehadiran
            </button>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const statusEl = document.getElementById('scan-status');

    function onScanSuccess(decodedText) {
        html5QrcodeScanner.clear();
        statusEl.textContent = 'QR berhasil dibaca, mengalihkan...';
        statusEl.classList.add('text-emerald-600');

        // The QR data is a URL like /absen/{token}
        try {
            const url = new URL(decodedText);
            window.location.href = url.pathname;
        } catch {
            // Fallback: treat decoded text as token or path directly
            window.location.href = '/absen/' + decodedText.replace(/.*\//, '');
        }
    }

    function onScanFailure() {
        // Silently ignore frame failures
    }

    const html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        {
            fps: 10,
            qrbox: { width: 220, height: 220 },
            rememberLastUsedCamera: true,
            showTorchButtonIfSupported: true,
        },
        false
    );

    html5QrcodeScanner.render(onScanSuccess, onScanFailure);

    // Update status once scanner initializes
    setTimeout(() => {
        statusEl.textContent = 'Arahkan kamera ke QR Code';
    }, 1500);
</script>
@endpush
