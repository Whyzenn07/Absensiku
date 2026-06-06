<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\MataKuliah;
use App\Models\Mahasiswa;
use App\Models\Sesi;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalMahasiswa  = Mahasiswa::count();
        $totalMataKuliah = MataKuliah::count();

        $totalSesiHariIni = Sesi::whereDate('started_at', today())->count();

        // Rata-rata kehadiran global
        $totalAbsensi = Absensi::count();
        $totalHadir   = Absensi::where('status', 'hadir')->count();
        $persentaseKehadiran = $totalAbsensi > 0
            ? round(($totalHadir / $totalAbsensi) * 100)
            : 0;

        // Sesi hari ini dengan relasi
        $sesiHariIni = Sesi::with(['mataKuliah', 'kelas', 'absensis'])
            ->whereDate('started_at', today())
            ->latest('started_at')
            ->take(5)
            ->get()
            ->map(function (Sesi $sesi) {
                $totalKelas = Mahasiswa::where('kelas_id', $sesi->kelas_id)->count();
                return [
                    'sesi'        => $sesi,
                    'hadir'       => $sesi->absensis->where('status', 'hadir')->count(),
                    'total_kelas' => $totalKelas,
                ];
            });

        // Fallback: tampilkan 3 sesi terakhir jika hari ini kosong
        if ($sesiHariIni->isEmpty()) {
            $sesiHariIni = Sesi::with(['mataKuliah', 'kelas', 'absensis'])
                ->latest('started_at')
                ->take(3)
                ->get()
                ->map(function (Sesi $sesi) {
                    $totalKelas = Mahasiswa::where('kelas_id', $sesi->kelas_id)->count();
                    return [
                        'sesi'        => $sesi,
                        'hadir'       => $sesi->absensis->where('status', 'hadir')->count(),
                        'total_kelas' => $totalKelas,
                    ];
                });
        }

        $tanggal = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y');

        return view('admin.dashboard', compact(
            'totalMahasiswa',
            'totalMataKuliah',
            'totalSesiHariIni',
            'persentaseKehadiran',
            'sesiHariIni',
            'tanggal',
        ));
    }
}
