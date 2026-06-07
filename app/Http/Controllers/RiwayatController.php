<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Sesi;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->input('search');
        $kelasId   = $request->input('kelas_id');
        $kelasList = Kelas::orderBy('nama')->get();

        $buildQuery = function () use ($search, $kelasId) {
            $q = Absensi::with(['mahasiswa.user', 'mahasiswa.kelas', 'mahasiswa.prodi', 'sesi.mataKuliah'])
                ->whereHas('sesi', fn ($s) => $s->where('user_id', auth()->id()));

            if ($kelasId) {
                $q->whereHas('mahasiswa', fn ($m) => $m->where('kelas_id', $kelasId));
            }
            if ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->whereHas('mahasiswa.user', fn ($m) => $m->where('name', 'like', "%{$search}%"))
                          ->orWhereHas('mahasiswa', fn ($m) => $m->where('nim', 'like', "%{$search}%"))
                          ->orWhereHas('sesi.mataKuliah', fn ($m) => $m->where('nama', 'like', "%{$search}%"));
                });
            }
            return $q;
        };

        $totalHadir = $buildQuery()->where('status', 'hadir')->count();
        $totalIzin  = $buildQuery()->where('status', 'izin')->count();
        $totalAlpha = $buildQuery()->where('status', 'alpha')->count();

        $absensis = $buildQuery()
            ->latest('waktu_scan')
            ->paginate(15)
            ->withQueryString();

        return view('admin.riwayat', compact(
            'absensis', 'kelasList', 'search', 'kelasId',
            'totalHadir', 'totalIzin', 'totalAlpha'
        ));
    }

    public function show(Sesi $sesi)
    {
        $sesi->load(['mataKuliah', 'kelas', 'absensis.mahasiswa.user']);

        return view('admin.riwayat-detail', compact('sesi'));
    }
}
