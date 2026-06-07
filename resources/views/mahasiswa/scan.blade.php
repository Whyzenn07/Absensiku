@extends('layouts.mobile')

@section('title', 'Scan QR – AbsensiKu')

@push('styles')
<style>
    /* Dark theme overrides for scan page */
    body { background: #0f172a !important; }
    /* Hide the global white header */
    body > div > header,
    body header.sticky { display: none !important; }
    /* Hide bottom nav */
    nav.bottom-nav { display: none !important; }
    /* Remove default bottom padding */
    main { padding-bottom: 0 !important; }

    /* QR reader dark styling */
    #reader { width: 100%; border-radius: 1rem; overflow: hidden; background: #1e293b; }
    #reader video { border-radius: 1rem; }
    #reader__scan_region { border-radius: 1rem; }
    #reader__dashboard { background: #1e293b !important; border-top: 1px solid #334155 !important; }
    #reader__dashboard_section_csr button {
        background: #2563eb !important;
        color: white !important;
        border-radius: 0.75rem !important;
        padding: 0.5rem 1rem !important;
        font-family: inherit !important;
        font-weight: 600 !important;
        border: none !important;
    }
    #reader__dashboard_section_csr span { color: #94a3b8 !important; font-size: 0.75rem !important; }
    #reader__header_message { display: none !important; }
    #reader__status_span { color: #64748b !important; }

    /* Scan line animation */
    @keyframes scanLine {
        0%   { top: 10%; }
        50%  { top: 85%; }
        100% { top: 10%; }
    }
    .scan-line {
        position: absolute;
        left: 0; right: 0;
        height: 2px;
        background: linear-gradient(to right, transparent, #ef4444, #ef4444, transparent);
        animation: scanLine 2s ease-in-out infinite;
        border-radius: 999px;
        box-shadow: 0 0 8px #ef4444;
    }
    .viewfinder {
        position: relative;
        background: #1e293b;
        border-radius: 1rem;
        overflow: hidden;
        min-height: 240px;
    }
    .viewfinder-corner {
        position: absolute;
        width: 20px; height: 20px;
        border-color: #60a5fa;
        border-style: solid;
    }
    .corner-tl { top: 12px; left: 12px; border-width: 3px 0 0 3px; border-radius: 4px 0 0 0; }
    .corner-tr { top: 12px; right: 12px; border-width: 3px 3px 0 0; border-radius: 0 4px 0 0; }
    .corner-bl { bottom: 12px; left: 12px; border-width: 0 0 3px 3px; border-radius: 0 0 0 4px; }
    .corner-br { bottom: 12px; right: 12px; border-width: 0 3px 3px 0; border-radius: 0 0 4px 0; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-900 flex flex-col">

    {{-- ── Dark Custom Header ───────────────────────────────────────────── --}}
    <div class="px-4 pt-4 pb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('mahasiswa.dashboard') }}"
               class="w-9 h-9 bg-slate-700/60 rounded-xl flex items-center justify-center text-slate-300 hover:bg-slate-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-base font-extrabold text-white">Scan QR Absensi</h1>
                <p class="text-xs text-slate-400">Arahkan ke QR Code dosen</p>
            </div>
        </div>
        <button class="w-9 h-9 bg-slate-700/60 rounded-xl flex items-center justify-center text-slate-300 hover:bg-slate-600 transition">
            <i class="fas fa-bolt text-sm"></i>
        </button>
    </div>

    {{-- ── Camera / Viewfinder Area ─────────────────────────────────────── --}}
    <div class="px-4 flex-1 space-y-4">
        {{-- Viewfinder placeholder (shows while camera initializes) --}}
        <div id="viewfinder-placeholder" class="viewfinder flex items-center justify-center" style="height:260px;">
            <div class="scan-line"></div>
            <div class="viewfinder-corner corner-tl"></div>
            <div class="viewfinder-corner corner-tr"></div>
            <div class="viewfinder-corner corner-bl"></div>
            <div class="viewfinder-corner corner-br"></div>
            <div class="text-center z-10">
                <i class="fas fa-camera text-slate-600 text-5xl mb-3 block"></i>
            </div>
        </div>

        {{-- QR scanner (injected here) --}}
        <div id="reader" class="hidden"></div>

        {{-- Status text --}}
        <div class="text-center space-y-1">
            <p id="scan-status" class="text-white font-semibold text-sm">Mendeteksi QR Code...</p>
            <p class="text-slate-400 text-xs">Arahkan kamera ke QR Code yang ditampilkan dosen</p>
        </div>

        {{-- Divider --}}
        <div class="flex items-center gap-3">
            <div class="flex-1 h-px bg-slate-700"></div>
            <span class="text-xs text-slate-500 font-semibold">atau masukkan token manual</span>
            <div class="flex-1 h-px bg-slate-700"></div>
        </div>

        {{-- Manual Token Form --}}
        <div class="bg-slate-800 rounded-2xl border border-slate-700 p-4">
            <form action="{{ route('absen.manual') }}" method="POST" class="space-y-3">
                @csrf
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-500">
                        <i class="fas fa-key text-sm"></i>
                    </span>
                    <input type="text" name="token" maxlength="8" required
                        class="w-full bg-slate-700 border border-slate-600 rounded-xl pl-10 pr-4 py-3 text-sm font-mono font-bold text-white tracking-widest uppercase focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-500"
                        placeholder="XXXXXXXX"
                        oninput="this.value = this.value.toUpperCase()">
                    @error('token') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit"
                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow">
                    <i class="fas fa-qrcode mr-2"></i>Simulasi Scan QR
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-500 pb-2">
            Tekan tombol di atas untuk demo absensi
        </p>
    </div>

    {{-- ── Footer ──────────────────────────────────────────────────────── --}}
    <div class="px-4 py-4 border-t border-slate-800 text-center">
        <p class="text-xs text-slate-600">Pastikan QR Code terlihat jelas dalam frame kamera</p>
        <p class="text-xs text-slate-700 mt-0.5">© 2026 AbsensiKu</p>
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
        statusEl.classList.add('text-emerald-400');
        try {
            const url = new URL(decodedText);
            window.location.href = url.pathname;
        } catch {
            window.location.href = '/absen/' + decodedText.replace(/.*\//, '');
        }
    }

    const html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { fps: 10, qrbox: { width: 220, height: 220 }, rememberLastUsedCamera: true },
        false
    );

    function initScanner() {
        document.getElementById('viewfinder-placeholder').classList.add('hidden');
        document.getElementById('reader').classList.remove('hidden');
        html5QrcodeScanner.render(onScanSuccess, () => {});
        setTimeout(() => { statusEl.textContent = 'Arahkan kamera ke QR Code'; }, 1500);
    }

    // Try to start camera automatically after short delay
    setTimeout(initScanner, 500);
</script>
@endpush
