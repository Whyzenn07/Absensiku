<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbsensiKu - Sistem Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }

        .btn-primary {
            background: #2563eb;
            transition: background-color 0.15s ease, box-shadow 0.15s ease;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.25);
        }
    </style>
</head>
<body class="antialiased">
    <nav class="fixed w-full z-50 bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="fas fa-qrcode text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-blue-700">AbsensiKu</span>
                </div>

                <div class="hidden md:flex items-center space-x-3">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="btn-primary px-5 py-2 rounded-xl text-white font-semibold text-sm shadow-sm">Dashboard</a>
                        @else
                            <a href="{{ route('mahasiswa.dashboard') }}" class="btn-primary px-5 py-2 rounded-xl text-white font-semibold text-sm shadow-sm">Dashboard</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2 rounded-xl text-gray-700 hover:bg-gray-50 font-semibold text-sm transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="btn-primary px-5 py-2 rounded-xl text-white font-semibold text-sm shadow-sm">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-28 pb-20 px-4">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            <section class="space-y-6">
                <div class="inline-flex items-center space-x-2 px-4 py-2 bg-blue-50 rounded-full text-blue-700 text-sm font-bold border border-blue-100">
                    <i class="fas fa-bolt"></i>
                    <span>Sistem Absensi Berbasis QR Code</span>
                </div>

                <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight">
                    Kelola Absensi Mahasiswa Lebih Cepat, Akurat, dan Terstruktur
                </h1>

                <p class="text-lg text-gray-600 leading-relaxed max-w-xl">
                    AbsensiKu membantu admin dalam mengelola data mahasiswa, mempermudah proses absensi digital, serta menyediakan rekap kehadiran secara otomatis dalam satu sistem terintegrasi.
                </p>

                <div class="flex items-start space-x-3 max-w-xl">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        Solusi modern untuk absensi kampus yang lebih efisien dan paperless.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('login') }}" class="px-6 py-3 rounded-2xl bg-white border border-gray-200 text-gray-800 font-bold hover:bg-gray-50 transition-colors flex items-center justify-center space-x-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Masuk</span>
                    </a>
                    <a href="{{ route('register') }}" class="btn-primary px-6 py-3 rounded-2xl text-white font-bold shadow-sm flex items-center justify-center space-x-2">
                        <i class="fas fa-rocket"></i>
                        <span>Mulai Sekarang</span>
                    </a>
                </div>
            </section>

            <section class="bg-white border border-gray-200 rounded-3xl shadow-sm p-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-center text-blue-700">
                            <i class="fas fa-sparkles"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Fitur Utama</p>
                            <p class="text-xs text-gray-500">Fokus pada kebutuhan absensi kampus</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-100 px-3 py-1 rounded-full">AbsensiKu</span>
                </div>

                <div class="mt-6">
                    <h2 class="text-2xl font-extrabold text-gray-900 leading-snug">
                        Fitur Unggulan AbsensiKu
                    </h2>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="p-5 bg-gray-50 rounded-2xl border border-gray-200">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 text-blue-700 flex items-center justify-center shrink-0">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Manajemen Data Mahasiswa</p>
                                <p class="text-gray-600 mt-1">Kelola data mahasiswa dengan mudah melalui sistem CRUD yang terstruktur.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 bg-gray-50 rounded-2xl border border-gray-200">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 text-blue-700 flex items-center justify-center shrink-0">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Absensi QR Code</p>
                                <p class="text-gray-600 mt-1">Proses absensi cepat hanya dengan scan QR melalui aplikasi mobile.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 bg-gray-50 rounded-2xl border border-gray-200">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 text-blue-700 flex items-center justify-center shrink-0">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Rekap Kehadiran Otomatis</p>
                                <p class="text-gray-600 mt-1">Data absensi tersimpan dan dapat dipantau secara real-time.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 bg-gray-50 rounded-2xl border border-gray-200">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 text-blue-700 flex items-center justify-center shrink-0">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Sistem Role (Admin & Mahasiswa)</p>
                                <p class="text-gray-600 mt-1">Hak akses terpisah untuk keamanan dan pengelolaan yang lebih baik.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer class="py-10 bg-gray-50 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 text-center text-sm text-gray-500">
            © 2026 AbsensiKu
        </div>
    </footer>
</body>
</html>
