<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'AbsensiKu')</title>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="AbsensiKu">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192.png">
    <link rel="icon" type="image/svg+xml" href="/icon.svg">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

        * { -webkit-tap-highlight-color: transparent; }

        /* Desktop: tampilkan frame abu di luar app */
        html {
            background-color: #cbd5e1;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: transparent;
        }

        /* App frame: terpusat, maksimal 480px, tinggi penuh viewport */
        #app-frame {
            background-color: #f1f5f9;
            max-width: 480px;
            width: 100%;
            margin: 0 auto;
            height: 100vh;
            height: 100dvh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.12);
        }

        /* Header tidak menyusut */
        #app-frame > header {
            flex-shrink: 0;
        }

        /* Area konten yang bisa di-scroll */
        #app-frame > main {
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Bottom nav mengikuti lebar frame */
        .bottom-nav {
            box-shadow: 0 -1px 12px rgba(0, 0, 0, 0.08);
            flex-shrink: 0;
        }

        .btn-primary { background: #2563eb; transition: background-color .15s, box-shadow .15s; }
        .btn-primary:hover, .btn-primary:active { background: #1d4ed8; }

        .badge-hadir  { background:#dcfce7; color:#16a34a; }
        .badge-izin   { background:#fef9c3; color:#ca8a04; }
        .badge-alpha  { background:#fee2e2; color:#dc2626; }
        .badge-aktif  { background:#dbeafe; color:#2563eb; }

        .card { background:#fff; border-radius:1rem; }
        .stat-card { background:#fff; border-radius:1rem; padding:1rem; }
    </style>
    @stack('styles')
</head>
<body>

<div id="app-frame">

    {{-- ── Top Header ─────────────────────────────────────────────────── --}}
    <header class="bg-white px-4 py-3 flex items-center justify-between z-30 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-qrcode text-white text-xs"></i>
            </div>
            <span class="font-bold text-blue-700 text-sm tracking-tight">AbsensiKu</span>
        </div>
        <div class="flex items-center gap-2">
            <button class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition">
                <i class="fas fa-bell text-sm"></i>
            </button>
            <a href="{{ auth()->user()->isAdmin() ? route('admin.profile') : route('mahasiswa.profile') }}"
               class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm shadow">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </a>
        </div>
    </header>

    {{-- ── Main Content ────────────────────────────────────────────────── --}}
    <main class="pb-24">
        @if(session('success'))
            <div class="mx-4 mt-4 flex items-center gap-2 p-3 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl text-sm font-medium">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mx-4 mt-4 flex items-center gap-2 p-3 bg-red-50 border border-red-100 text-red-700 rounded-xl text-sm font-medium">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    {{-- ── Bottom Navigation ───────────────────────────────────────────── --}}
    <nav class="bottom-nav bg-white z-30">
        <div class="flex items-center justify-around px-2 py-2">

            {{-- Beranda --}}
            @if(auth()->user()->isAdmin())
                @php $berandaRoute = route('admin.dashboard'); $isBerandaActive = request()->routeIs('admin.dashboard'); @endphp
            @else
                @php $berandaRoute = route('mahasiswa.dashboard'); $isBerandaActive = request()->routeIs('mahasiswa.dashboard'); @endphp
            @endif

            <a href="{{ $berandaRoute }}"
               class="flex flex-col items-center gap-1 flex-1 py-1 {{ $isBerandaActive ? 'text-blue-600' : 'text-slate-400' }}">
                <i class="fas fa-home text-xl"></i>
                <span class="text-[10px] font-semibold">Beranda</span>
            </a>

            {{-- QR (tengah, menonjol) --}}
            @if(auth()->user()->isAdmin())
                @php $qrRoute = route('admin.qr.create'); $isQrActive = request()->routeIs('admin.qr.*'); @endphp
            @else
                @php $qrRoute = route('mahasiswa.scan'); $isQrActive = request()->routeIs('mahasiswa.scan'); @endphp
            @endif

            <a href="{{ $qrRoute }}"
               class="flex flex-col items-center flex-1 -mt-5">
                <div class="w-14 h-14 rounded-full flex items-center justify-center shadow-lg {{ $isQrActive ? 'bg-blue-700' : 'bg-blue-600' }}">
                    <i class="fas fa-qrcode text-white text-2xl"></i>
                </div>
                <span class="text-[10px] font-semibold mt-1 {{ $isQrActive ? 'text-blue-600' : 'text-slate-400' }}">QR</span>
            </a>

            {{-- Profile --}}
            @if(auth()->user()->isAdmin())
                @php $profileRoute = route('admin.profile'); $isProfileActive = request()->routeIs('admin.profile'); @endphp
            @else
                @php $profileRoute = route('mahasiswa.profile'); $isProfileActive = request()->routeIs('mahasiswa.profile'); @endphp
            @endif

            <a href="{{ $profileRoute }}"
               class="flex flex-col items-center gap-1 flex-1 py-1 {{ $isProfileActive ? 'text-blue-600' : 'text-slate-400' }}">
                <i class="fas fa-user text-xl"></i>
                <span class="text-[10px] font-semibold">Profile</span>
            </a>
        </div>
    </nav>

</div>{{-- #app-frame --}}

@stack('scripts')
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    }
</script>
</body>
</html>
