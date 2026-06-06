<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MahasiswaDashboardController extends Controller
{
    public function index()
    {
        $mahasiswa = auth()->user()->mahasiswa()->with(['prodi', 'kelas'])->firstOrFail();

        $totalAbsensi = $mahasiswa->absensis()->count();
        $totalHadir   = $mahasiswa->absensis()->where('status', 'hadir')->count();
        $totalIzin    = $mahasiswa->absensis()->where('status', 'izin')->count();
        $totalAlpha   = $mahasiswa->absensis()->where('status', 'alpha')->count();
        $persentase   = $totalAbsensi > 0 ? round($totalHadir / $totalAbsensi * 100) : 0;

        $hariMap = [1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu'];
        $hariIni = $hariMap[Carbon::now()->dayOfWeek] ?? null;

        $jadwalHariIni = $hariIni
            ? Jadwal::with('mataKuliah')
                ->where('kelas_id', $mahasiswa->kelas_id)
                ->where('hari', $hariIni)
                ->orderBy('jam_mulai')
                ->get()
            : collect();

        $riwayatTerakhir = $mahasiswa->absensis()
            ->with(['sesi.mataKuliah', 'sesi.kelas'])
            ->latest('waktu_scan')
            ->limit(3)
            ->get();

        return view('mahasiswa.dashboard', compact(
            'mahasiswa',
            'totalAbsensi', 'totalHadir', 'totalIzin', 'totalAlpha', 'persentase',
            'jadwalHariIni',
            'riwayatTerakhir'
        ));
    }
}
