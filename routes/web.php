<?php

use App\Http\Controllers\AbsenController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaDashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\RiwayatController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// ── Setup Route (HAPUS setelah deploy pertama) ────────────────────────────
Route::get('/setup-absensiku-db-2024', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        $out = Artisan::output();
        Artisan::call('db:seed', ['--force' => true]);
        $out .= Artisan::output();
        return response('<pre style="font-family:monospace;padding:2rem;">✅ BERHASIL!<br>' . htmlspecialchars($out) . '<br>⚠️ Hapus route ini dari routes/web.php sekarang!</pre>');
    } catch (\Throwable $e) {
        return response('<pre style="color:red;padding:2rem;">❌ ERROR: ' . htmlspecialchars($e->getMessage()) . '</pre>', 500);
    }
});

// ── Landing Page ──────────────────────────────────────────────────────────
Route::get('/', fn () => view('welcome'));

// ── Guest Routes ──────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',            [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',           [AuthController::class, 'login']);
    Route::get('/register',         [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',        [AuthController::class, 'register']);
    Route::get('/forgot-password',  [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'resetPasswordSimple'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Authenticated Routes ──────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // ── Admin ─────────────────────────────────────────────────────────────
    Route::middleware('can:admin-only')->prefix('admin')->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Manajemen Mahasiswa
        Route::resource('mahasiswa', MahasiswaController::class);

        // Generate QR
        Route::get('/qr/create',            [QrController::class, 'create'])->name('admin.qr.create');
        Route::post('/qr',                  [QrController::class, 'store'])->name('admin.qr.store');
        Route::get('/qr/{sesi}',            [QrController::class, 'show'])->name('admin.qr.show');
        Route::get('/qr/{sesi}/refresh',    [QrController::class, 'refresh'])->name('admin.qr.refresh');
        Route::post('/qr/{sesi}/selesai',   [QrController::class, 'selesai'])->name('admin.qr.selesai');
        Route::post('/qr/{sesi}/sesi-baru', [QrController::class, 'sesiBaru'])->name('admin.qr.sesi-baru');

        // Riwayat absensi
        Route::get('/riwayat',        [RiwayatController::class, 'index'])->name('admin.riwayat');
        Route::get('/riwayat/{sesi}', [RiwayatController::class, 'show'])->name('admin.riwayat.show');

        // Profile
        Route::get('/profile',          [ProfileController::class, 'adminProfile'])->name('admin.profile');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.profile.password');
    });

    // ── Mahasiswa ─────────────────────────────────────────────────────────
    Route::middleware('can:mahasiswa-only')->prefix('mahasiswa')->group(function () {
        Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('mahasiswa.dashboard');
        Route::get('/scan',      fn () => view('mahasiswa.scan'))->name('mahasiswa.scan');
        Route::get('/riwayat',   [AbsenController::class, 'riwayat'])->name('mahasiswa.riwayat');
        Route::get('/profile',   [ProfileController::class, 'mahasiswaProfile'])->name('mahasiswa.profile');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('mahasiswa.profile.password');
    });

    // ── Absensi QR Scan ───────────────────────────────────────────────────
    Route::get('/absen/{token}',  [AbsenController::class, 'scan'])->name('absen.scan');
    Route::post('/absen/manual',  [AbsenController::class, 'manual'])->name('absen.manual');
});
