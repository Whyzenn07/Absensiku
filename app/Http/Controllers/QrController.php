<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Sesi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QrController extends Controller
{
    public function create()
    {
        $mataKuliahs = MataKuliah::where('user_id', auth()->id())->orderBy('nama')->get();
        $kelas       = Kelas::orderBy('nama')->get();

        return view('admin.qr.create', compact('mataKuliahs', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mata_kuliah_id' => ['required', 'exists:mata_kuliahs,id'],
            'kelas_id'       => ['required', 'exists:kelas,id'],
            'durasi'         => ['required', 'integer', 'min:1', 'max:300'],
        ], [
            'mata_kuliah_id.required' => 'Mata kuliah wajib dipilih.',
            'kelas_id.required'       => 'Kelas wajib dipilih.',
            'durasi.required'         => 'Durasi wajib diisi.',
            'durasi.min'              => 'Durasi minimal 1 menit.',
            'durasi.max'              => 'Durasi maksimal 300 menit.',
        ]);

        // Nonaktifkan sesi aktif yang sudah kadaluarsa
        Sesi::where('user_id', auth()->id())
            ->where('status', 'aktif')
            ->where('started_at', '<', now()->subMinutes(300))
            ->update(['status' => 'selesai']);

        do {
            $token = strtoupper(Str::random(8));
        } while (Sesi::where('token', $token)->exists());

        $sesi = Sesi::create([
            'mata_kuliah_id' => $request->mata_kuliah_id,
            'kelas_id'       => $request->kelas_id,
            'user_id'        => auth()->id(),
            'token'          => $token,
            'qr_data'        => url('/absen/' . $token),
            'durasi'         => $request->durasi,
            'status'         => 'aktif',
            'started_at'     => now(),
        ]);

        return redirect()->route('admin.qr.show', $sesi);
    }

    public function show(Sesi $sesi)
    {
        $sesi->load(['mataKuliah', 'kelas', 'absensis']);

        $totalKelas = Mahasiswa::where('kelas_id', $sesi->kelas_id)->count();
        $hadir      = $sesi->absensis->where('status', 'hadir')->count();
        $tidak      = $totalKelas - $hadir;

        // Sisa waktu dalam detik
        $sisaDetik = max(0, $sesi->started_at->addMinutes($sesi->durasi)->diffInSeconds(now(), false) * -1);

        if ($sesi->status === 'aktif' && $sisaDetik <= 0) {
            $sesi->update(['status' => 'selesai', 'ended_at' => now()]);
            $sesi->refresh();
        }

        return view('admin.qr.show', compact('sesi', 'hadir', 'tidak', 'totalKelas', 'sisaDetik'));
    }

    public function refresh(Sesi $sesi)
    {
        $sesi->load('absensis');

        $totalKelas = Mahasiswa::where('kelas_id', $sesi->kelas_id)->count();
        $hadir      = $sesi->absensis->where('status', 'hadir')->count();
        $tidak      = $totalKelas - $hadir;

        $sisaDetik = max(0, $sesi->started_at->addMinutes($sesi->durasi)->diffInSeconds(now(), false) * -1);

        if ($sesi->status === 'aktif' && $sisaDetik <= 0) {
            $sesi->update(['status' => 'selesai', 'ended_at' => now()]);
            $sisaDetik = 0;
        }

        return response()->json([
            'hadir'      => $hadir,
            'tidak'      => $tidak,
            'total'      => $totalKelas,
            'sisa_detik' => (int) $sisaDetik,
            'status'     => $sesi->fresh()->status,
        ]);
    }

    public function selesai(Sesi $sesi)
    {
        $sesi->update(['status' => 'selesai', 'ended_at' => now()]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Sesi absensi telah diselesaikan.');
    }

    public function sesiBaru(Sesi $sesi)
    {
        $sesi->update(['status' => 'selesai', 'ended_at' => now()]);

        return redirect()->route('admin.qr.create');
    }
}
