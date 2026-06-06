<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbsensiKu - Modern Attendance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }

        .glass-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
        }

        .sidebar-link {
            transition: color 0.15s ease, background-color 0.15s ease;
        }

        .btn-primary {
            background: #2563eb;
            transition: background-color 0.15s ease, box-shadow 0.15s ease;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.25);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #c7d2fe;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #818cf8;
        }
    </style>
</head>
<body class="overflow-x-hidden">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @auth
        <aside id="sidebar" class="hidden lg:flex flex-col w-64 bg-white border-r border-gray-100 shadow-sm z-30 transition-all duration-300">
            <div class="p-6 flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                    <i class="fas fa-qrcode text-white text-xl"></i>
                </div>
                <span class="text-xl font-bold text-blue-700">AbsensiKu</span>
            </div>

            <nav class="flex-1 px-4 space-y-2 mt-4 overflow-y-auto">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Menu Utama</p>
                
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                        <i class="fas fa-home w-6"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('mahasiswa.index') }}" class="sidebar-link flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('mahasiswa.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                        <i class="fas fa-users w-6"></i>
                        <span>Data Mahasiswa</span>
                    </a>
                @else
                    <a href="{{ route('mahasiswa.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                        <i class="fas fa-home w-6"></i>
                        <span>Dashboard</span>
                    </a>
                @endif

                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-2">Absensi</p>
                <div class="opacity-50 pointer-events-none px-4 py-3 text-sm text-gray-400">
                    <i class="fas fa-lock mr-2"></i> Coming Soon
                </div>
            </nav>

            <div class="p-4 border-t border-gray-50">
                <div class="bg-gray-50 rounded-2xl p-4 flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold shadow-sm">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                </div>
            </div>
        </aside>
        @endauth

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Navbar -->
            <header class="bg-white border-b border-gray-100 h-16 flex items-center justify-between px-4 lg:px-8 z-20">
                <div class="flex items-center space-x-4">
                    <button id="mobile-menu-btn" class="lg:hidden p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-lg font-semibold text-gray-800 hidden md:block">
                        @yield('title', 'Welcome back!')
                    </h2>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-red-600 hover:bg-red-50 transition-colors font-medium text-sm">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    @else
                        <div class="flex space-x-2">
                            <a href="{{ route('login') }}" class="px-5 py-2 rounded-xl text-gray-600 hover:bg-gray-50 font-medium text-sm">Login</a>
                            <a href="{{ route('register') }}" class="btn-primary px-5 py-2 rounded-xl text-white font-medium text-sm shadow-sm">Daftar</a>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-8">
                @if(session('success'))
                    <div class="mb-6 flex items-center p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl shadow-sm">
                        <i class="fas fa-check-circle mr-3 text-lg"></i>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                @endif

                <div>
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        // Simple Mobile Menu Toggle
        const menuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        
        if(menuBtn) {
            menuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('hidden');
                sidebar.classList.toggle('flex');
                sidebar.classList.toggle('fixed');
                sidebar.classList.toggle('inset-0');
            });
        }
    </script>
</body>
</html>
