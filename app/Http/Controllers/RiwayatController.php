<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Sesi;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->input('search');
        $kelasId   = $request->input('kelas_id');
        $mkId      = $request->input('mk_id');
        $kelasList = Kelas::orderBy('nama')->get();
        $mkList    = MataKuliah::where('user_id', auth()->id())->orderBy('nama')->get();

        $sesis = Sesi::with(['mataKuliah', 'kelas', 'absensis'])
            ->where('user_id', auth()->id())
            ->when($kelasId, fn ($q) => $q->where('kelas_id', $kelasId))
            ->when($mkId,    fn ($q) => $q->where('mata_kuliah_id', $mkId))
            ->when($search,  function ($q) use ($search) {
                $q->whereHas('mataKuliah', fn ($m) => $m->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('kelas',    fn ($k) => $k->where('nama', 'like', "%{$search}%"));
            })
            ->latest('started_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.riwayat', compact('sesis', 'kelasList', 'mkList', 'search', 'kelasId', 'mkId'));
    }

    public function show(Sesi $sesi)
    {
        $sesi->load(['mataKuliah', 'kelas', 'absensis.mahasiswa.user']);

        return view('admin.riwayat-detail', compact('sesi'));
    }
}
