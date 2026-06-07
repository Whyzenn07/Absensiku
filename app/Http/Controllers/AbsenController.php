<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Sesi;
use Illuminate\Http\Request;

class AbsenController extends Controller
{
    public function scan(string $token)
    {
        $sesi = Sesi::with(['mataKuliah', 'kelas'])->where('token', $token)->firstOrFail();

        if (!$sesi->isAktif()) {
            return view('absen.hasil', [
                'status'  => 'expired',
                'pesan'   => 'Sesi absensi sudah berakhir atau tidak aktif.',
                'sesi'    => $sesi,
            ]);
        }

        $mahasiswa = auth()->user()->mahasiswa()->with('kelas')->firstOrFail();

        if ($mahasiswa->kelas_id !== $sesi->kelas_id) {
            return view('absen.hasil', [
                'status'  => 'salah_kelas',
                'pesan'   => 'Kelas Anda tidak sesuai dengan sesi ini.',
                'sesi'    => $sesi,
            ]);
        }

        $sudah = Absensi::where('sesi_id', $sesi->id)
                        ->where('mahasiswa_id', $mahasiswa->id)
                        ->exists();

        if ($sudah) {
            return view('absen.hasil', [
                'status'  => 'duplikat',
                'pesan'   => 'Anda sudah tercatat hadir di sesi ini.',
                'sesi'    => $sesi,
            ]);
        }

        Absensi::create([
            'sesi_id'     => $sesi->id,
            'mahasiswa_id'=> $mahasiswa->id,
            'status'      => 'hadir',
            'waktu_scan'  => now(),
        ]);

        return view('absen.hasil', [
            'status'  => 'sukses',
            'pesan'   => 'Kehadiran Anda berhasil dicatat!',
            'sesi'    => $sesi,
        ]);
    }

    public function manual(Request $request)
    {
        $request->validate(['token' => ['required', 'string', 'size:8']]);

        return redirect()->route('absen.scan', $request->token);
    }

    public function riwayat(Request $request)
    {
        $mahasiswa = auth()->user()->mahasiswa()->firstOrFail();
        $search    = $request->input('search');

        $totalHadir = $mahasiswa->absensis()->where('status', 'hadir')->count();
        $totalIzin  = $mahasiswa->absensis()->where('status', 'izin')->count();
        $totalAlpha = $mahasiswa->absensis()->where('status', 'alpha')->count();

        $absensis = $mahasiswa->absensis()
            ->with(['sesi.mataKuliah'])
            ->when($search, fn ($q) => $q->whereHas('sesi.mataKuliah', fn ($m) => $m->where('nama', 'like', "%{$search}%")))
            ->latest('waktu_scan')
            ->paginate(20)
            ->withQueryString();

        return view('mahasiswa.riwayat', compact('absensis', 'totalHadir', 'totalIzin', 'totalAlpha', 'search'));
    }
}
