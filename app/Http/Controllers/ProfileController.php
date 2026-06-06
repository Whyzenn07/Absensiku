<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function adminProfile()
    {
        return view('admin.profile', ['user' => auth()->user()]);
    }

    public function mahasiswaProfile()
    {
        $mahasiswa = auth()->user()->mahasiswa()->with(['prodi', 'kelas'])->firstOrFail();
        return view('mahasiswa.profile', compact('mahasiswa'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama' => ['required', 'string'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        if (!Hash::check($request->password_lama, auth()->user()->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
