<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        \Log::info('[LOGIN] start email=' . $request->input('email'));

        $credentials = $request->validate(
            [
                'email'    => ['required', 'email'],
                'password' => ['required'],
            ],
            [
                'email.required'    => 'Email wajib diisi.',
                'email.email'       => 'Format email tidak valid.',
                'password.required' => 'Password wajib diisi.',
            ]
        );

        \Log::info('[LOGIN] credentials validated');

        if (Auth::attempt($credentials)) {
            \Log::info('[LOGIN] attempt success');
            $request->session()->regenerate();
            \Log::info('[LOGIN] session regenerated');

            $isAdmin = Auth::user()->isAdmin();
            \Log::info('[LOGIN] isAdmin=' . ($isAdmin ? 'true' : 'false'));

            return $isAdmin
                ? redirect()->intended(route('admin.dashboard'))
                : redirect()->intended(route('mahasiswa.dashboard'));
        }

        \Log::info('[LOGIN] attempt failed');
        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function showRegister()
    {
        $prodis = Prodi::orderBy('nama')->get();
        $kelas  = Kelas::orderBy('nama')->get();

        return view('auth.register', compact('prodis', 'kelas'));
    }

    public function register(Request $request)
    {
        $request->validate(
            [
                'name'      => ['required', 'string', 'max:255'],
                'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password'  => ['required', 'string', 'min:8', 'confirmed'],
                'nim'       => ['required', 'string', 'unique:mahasiswas'],
                'no_hp'     => ['nullable', 'string', 'max:20'],
                'prodi_id'  => ['required', 'exists:prodis,id'],
                'kelas_id'  => ['required', 'exists:kelas,id'],
            ],
            [
                'name.required'     => 'Nama wajib diisi.',
                'email.required'    => 'Email wajib diisi.',
                'email.email'       => 'Format email tidak valid.',
                'email.unique'      => 'Email sudah terdaftar.',
                'password.required' => 'Password wajib diisi.',
                'password.min'      => 'Password minimal 8 karakter.',
                'password.confirmed'=> 'Konfirmasi password tidak sama.',
                'nim.required'      => 'NIM wajib diisi.',
                'nim.unique'        => 'NIM sudah terdaftar.',
                'prodi_id.required' => 'Program studi wajib dipilih.',
                'prodi_id.exists'   => 'Program studi tidak valid.',
                'kelas_id.required' => 'Kelas wajib dipilih.',
                'kelas_id.exists'   => 'Kelas tidak valid.',
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

        return redirect()->route('login')
            ->with('status', 'Registrasi berhasil. Silakan login untuk melanjutkan.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function resetPasswordSimple(Request $request)
    {
        $request->validate(
            [
                'email'    => ['required', 'email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ],
            [
                'email.required'    => 'Email wajib diisi.',
                'email.email'       => 'Format email tidak valid.',
                'password.required' => 'Password baru wajib diisi.',
                'password.min'      => 'Password minimal 8 karakter.',
                'password.confirmed'=> 'Konfirmasi password tidak sama.',
            ]
        );

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.'])->withInput($request->only('email'));
        }

        $user->forceFill(['password' => Hash::make($request->password)])->save();

        return redirect()->route('login')->with('status', 'Password berhasil direset. Silakan login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
