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
        select.input-field { background-image: none; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col">

    {{-- Header --}}
    <header class="w-full bg-white border-b border-slate-100 px-4 py-3 flex items-center space-x-2 shadow-sm">
        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
            <i class="fas fa-th-large text-white text-sm"></i>
        </div>
        <span class="text-base font-bold text-blue-700 tracking-tight">AbsensiKu</span>
    </header>

    {{-- Main --}}
    <main class="flex-1 flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-sm">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }
    </script>
</body>
</html>
