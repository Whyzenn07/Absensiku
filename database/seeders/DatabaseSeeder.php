<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\Sesi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin / Dosen ──────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@telkom-university.ac.id'],
            [
                'name'     => 'Dr. Wayy Pratama',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // ── Prodis & Kelas ─────────────────────────────────────────────────
        $prodis = Prodi::all()->keyBy('nama');
        $kelas  = Kelas::all()->keyBy('nama');

        // ── Mata Kuliah ────────────────────────────────────────────────────
        $mkData = [
            ['nama' => 'Pemrograman Web',         'kode' => 'IF101'],
            ['nama' => 'Basis Data',               'kode' => 'IF102'],
            ['nama' => 'Jaringan Komputer',        'kode' => 'IF103'],
            ['nama' => 'Sistem Operasi',           'kode' => 'IF104'],
            ['nama' => 'Algoritma & Pemrograman',  'kode' => 'IF105'],
            ['nama' => 'Rekayasa Perangkat Lunak', 'kode' => 'IF106'],
        ];

        $mataKuliahs = collect($mkData)->map(fn ($mk) =>
            MataKuliah::firstOrCreate(
                ['kode' => $mk['kode']],
                ['nama' => $mk['nama'], 'user_id' => $admin->id]
            )
        );

        // ── Jadwal ────────────────────────────────────────────────────────
        $jadwalData = [
            [$mataKuliahs[0]->id, $kelas['IF-01']->id, 'senin',  '08:00', '09:40'],
            [$mataKuliahs[1]->id, $kelas['IF-01']->id, 'selasa', '10:30', '12:10'],
            [$mataKuliahs[2]->id, $kelas['IF-01']->id, 'rabu',   '13:00', '14:40'],
            [$mataKuliahs[3]->id, $kelas['IF-02']->id, 'kamis',  '08:00', '09:40'],
            [$mataKuliahs[4]->id, $kelas['IF-02']->id, 'jumat',  '10:30', '12:10'],
            [$mataKuliahs[5]->id, $kelas['SI-01']->id, 'senin',  '13:00', '14:40'],
        ];

        foreach ($jadwalData as [$mkId, $kelasId, $hari, $mulai, $selesai]) {
            Jadwal::firstOrCreate(
                ['mata_kuliah_id' => $mkId, 'kelas_id' => $kelasId, 'hari' => $hari],
                ['jam_mulai' => $mulai, 'jam_selesai' => $selesai]
            );
        }

        // ── Mahasiswa ──────────────────────────────────────────────────────
        $mahasiswaData = [
            ['name' => 'Neilsya Putri',    'email' => 'neilsyaputri@student.telkom.ac.id', 'nim' => '103042310048',  'prodi' => 'Informatika',      'kelas' => 'IF-02'],
            ['name' => 'Rivaldo Tandoko',  'email' => 'rivaldo@student.telkom.ac.id',       'nim' => '1034217006',    'prodi' => 'Informatika',      'kelas' => 'IF-02'],
            ['name' => 'Wahyu Pratama',    'email' => 'wahyupratama@student.telkom.ac.id',  'nim' => '1034217008',    'prodi' => 'Informatika',      'kelas' => 'IF-02'],
            ['name' => 'Aiqbal Hermawan',  'email' => 'aiqbal@student.telkom.ac.id',        'nim' => '1034219030',    'prodi' => 'Informatika',      'kelas' => 'IF-02'],
            ['name' => 'Wahyu Argo Mulyo', 'email' => 'wahyuargomu123@gmail.com',           'nim' => '1034219045',    'prodi' => 'Sistem Informasi', 'kelas' => 'SI-01'],
            ['name' => 'Muhammad Farhan',  'email' => 'farhan@student.telkom.ac.id',        'nim' => '1034221008',    'prodi' => 'Informatika',      'kelas' => 'IF-01'],
        ];

        $mahasiswaModels = [];
        foreach ($mahasiswaData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('password'),
                    'role'     => 'mahasiswa',
                ]
            );

            $mahasiswaModels[] = Mahasiswa::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nim'      => $data['nim'],
                    'no_hp'    => '085000000000',
                    'prodi_id' => $prodis[$data['prodi']]->id,
                    'kelas_id' => $kelas[$data['kelas']]->id,
                ]
            );
        }

        // ── Sesi & Absensi (hanya buat jika belum ada data) ───────────────
        if (Sesi::count() === 0) {
            $statusOptions = ['hadir', 'hadir', 'hadir', 'hadir', 'izin', 'alpha'];

            for ($i = 5; $i >= 1; $i--) {
                $tanggal = now()->subDays($i);
                $mk      = $mataKuliahs->random();

                $sesi = Sesi::create([
                    'mata_kuliah_id' => $mk->id,
                    'kelas_id'       => $kelas['IF-02']->id,
                    'user_id'        => $admin->id,
                    'token'          => strtoupper(Str::random(8)),
                    'qr_data'        => Str::uuid(),
                    'durasi'         => 90,
                    'status'         => 'selesai',
                    'started_at'     => $tanggal->copy()->setTime(8, 0),
                    'ended_at'       => $tanggal->copy()->setTime(9, 30),
                ]);

                foreach (array_slice($mahasiswaModels, 0, 4) as $mhs) {
                    Absensi::firstOrCreate(
                        ['sesi_id' => $sesi->id, 'mahasiswa_id' => $mhs->id],
                        [
                            'status'     => $statusOptions[array_rand($statusOptions)],
                            'waktu_scan' => $tanggal->copy()->setTime(8, rand(1, 15)),
                        ]
                    );
                }
            }
        }
    }
}
