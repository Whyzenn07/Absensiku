<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $kelasId  = $request->input('kelas_id');
        $kelasList = Kelas::orderBy('nama')->get();

        $mahasiswas = Mahasiswa::with(['user', 'prodi', 'kelas'])
            ->when($search, function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                                                      ->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('prodi', fn ($p) => $p->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('kelas', fn ($k) => $k->where('nama', 'like', "%{$search}%"));
            })
            ->when($kelasId, fn ($q) => $q->where('kelas_id', $kelasId))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.mahasiswa.index', compact('mahasiswas', 'search', 'kelasList', 'kelasId'));
    }

    public function create()
    {
        $prodis = Prodi::orderBy('nama')->get();
        $kelas  = Kelas::orderBy('nama')->get();

        return view('admin.mahasiswa.create', compact('prodis', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
                'nim'      => ['required', 'string', 'unique:mahasiswas'],
                'no_hp'    => ['nullable', 'string', 'max:20'],
                'prodi_id' => ['required', 'exists:prodis,id'],
                'kelas_id' => ['required', 'exists:kelas,id'],
            ],
            [
                'name.required'     => 'Nama wajib diisi.',
                'email.required'    => 'Email wajib diisi.',
                'email.unique'      => 'Email sudah terdaftar.',
                'password.required' => 'Password wajib diisi.',
                'password.min'      => 'Password minimal 8 karakter.',
                'nim.required'      => 'NIM wajib diisi.',
                'nim.unique'        => 'NIM sudah terdaftar.',
                'prodi_id.required' => 'Program studi wajib dipilih.',
                'kelas_id.required' => 'Kelas wajib dipilih.',
            ]
        );

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'mahasiswa',
            ]);

            Mahasiswa::create([
                'user_id'  => $user->id,
                'nim'      => $request->nim,
                'no_hp'    => $request->no_hp,
                'prodi_id' => $request->prodi_id,
                'kelas_id' => $request->kelas_id,
            ]);
        });

        return redirect()->route('mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $prodis = Prodi::orderBy('nama')->get();
        $kelas  = Kelas::orderBy('nama')->get();

        return view('admin.mahasiswa.edit', compact('mahasiswa', 'prodis', 'kelas'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $request->validate(
            [
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $mahasiswa->user_id],
                'nim'      => ['required', 'string', 'unique:mahasiswas,nim,' . $mahasiswa->id],
                'no_hp'    => ['nullable', 'string', 'max:20'],
                'prodi_id' => ['required', 'exists:prodis,id'],
                'kelas_id' => ['required', 'exists:kelas,id'],
                'password' => ['nullable', 'string', 'min:8'],
            ],
            [
                'name.required'     => 'Nama wajib diisi.',
                'email.required'    => 'Email wajib diisi.',
                'email.unique'      => 'Email sudah terdaftar.',
                'nim.required'      => 'NIM wajib diisi.',
                'nim.unique'        => 'NIM sudah terdaftar.',
                'prodi_id.required' => 'Program studi wajib dipilih.',
                'kelas_id.required' => 'Kelas wajib dipilih.',
                'password.min'      => 'Password minimal 8 karakter.',
            ]
        );

        DB::transaction(function () use ($request, $mahasiswa) {
            $mahasiswa->user->update([
                'name'  => $request->name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $mahasiswa->user->update(['password' => Hash::make($request->password)]);
            }

            $mahasiswa->update([
                'nim'      => $request->nim,
                'no_hp'    => $request->no_hp,
                'prodi_id' => $request->prodi_id,
                'kelas_id' => $request->kelas_id,
            ]);
        });

        return redirect()->route('mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->user->delete();

        return redirect()->route('mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil dihapus.');
    }
}
