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
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="AbsensiKu">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/icons/icon-512.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192.png">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
        .btn-primary { background: #2563eb; transition: background-color 0.15s ease, box-shadow 0.15s ease; }
        .btn-primary:hover { background: #1d4ed8; box-shadow: 0 10px 15px -3px rgba(37,99,235,0.25); }
        .input-field {
            appearance: none; display: block; width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            background: #f8fafc; border: 1.5px solid #e2e8f0;
            border-radius: 0.875rem; color: #1e293b; font-size: 0.875rem;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }
        .input-field:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.12); }
        .input-field.error { border-color: #ef4444; }

        /* PWA install banner */
        #pwa-banner {
            display: none;
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 999;
            background: #1e293b; color: #fff;
            padding: 14px 16px;
            align-items: center; gap: 12px;
            box-shadow: 0 -4px 24px rgba(0,0,0,0.18);
            animation: slideUp .3s ease;
        }
        #pwa-banner.show { display: flex; }
        @keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
        #pwa-banner .pwa-icon {
            width: 44px; height: 44px; min-width: 44px;
            background: #2563eb; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
        }
        #pwa-banner .pwa-text { flex: 1; }
        #pwa-banner .pwa-text strong { display: block; font-size: 13px; font-weight: 700; }
        #pwa-banner .pwa-text span { font-size: 11px; color: #94a3b8; }
        #pwa-banner .pwa-install {
            background: #2563eb; color: #fff; border: none;
            border-radius: 8px; padding: 8px 16px;
            font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap;
        }
        #pwa-banner .pwa-close {
            background: none; border: none; color: #64748b;
            font-size: 18px; cursor: pointer; padding: 4px;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col">

    {{-- Header --}}
    <header class="w-full bg-white border-b border-slate-100 px-4 py-3 flex items-center space-x-2 shadow-sm">
        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
            <i class="fas fa-qrcode text-white text-sm"></i>
        </div>
        <span class="text-base font-bold text-blue-700 tracking-tight">AbsensiKu</span>
    </header>

    {{-- Main --}}
    <main class="flex-1 flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-sm">
            @yield('content')
        </div>
    </main>

    {{-- PWA Install Banner --}}
    <div id="pwa-banner">
        <div class="pwa-icon">&#128247;</div>
        <div class="pwa-text">
            <strong>Install AbsensiKu</strong>
            <span>Tambahkan ke layar utama HP</span>
        </div>
        <button class="pwa-install" id="pwa-install-btn">Install</button>
        <button class="pwa-close" id="pwa-close-btn">&#x2715;</button>
    </div>

    @stack('scripts')
    <script>
        // Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }

        // PWA Install prompt
        let deferredPrompt;
        const banner  = document.getElementById('pwa-banner');
        const btnInst = document.getElementById('pwa-install-btn');
        const btnClose= document.getElementById('pwa-close-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            banner.classList.add('show');
        });

        btnInst.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            deferredPrompt = null;
            banner.classList.remove('show');
        });

        btnClose.addEventListener('click', () => {
            banner.classList.remove('show');
            sessionStorage.setItem('pwa-dismissed', '1');
        });

        if (sessionStorage.getItem('pwa-dismissed')) {
            banner.classList.remove('show');
        }

        window.addEventListener('appinstalled', () => {
            banner.classList.remove('show');
        });
    </script>
</body>
</html>
