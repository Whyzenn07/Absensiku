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
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Admin / Dosen ──────────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Dr. Wayy Pratama',
            'email'    => 'admin@telkom-university.ac.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // ── Prodis & Kelas sudah di-seed di migration ──────────────────────
        $prodis = Prodi::all()->keyBy('nama');
        $kelas  = Kelas::all()->keyBy('nama');

        // ── Mata Kuliah ────────────────────────────────────────────────────
        $mataKuliahs = collect([
            ['nama' => 'Pemrograman Web',        'kode' => 'IF101'],
            ['nama' => 'Basis Data',              'kode' => 'IF102'],
            ['nama' => 'Jaringan Komputer',       'kode' => 'IF103'],
            ['nama' => 'Sistem Operasi',          'kode' => 'IF104'],
            ['nama' => 'Algoritma & Pemrograman', 'kode' => 'IF105'],
            ['nama' => 'Rekayasa Perangkat Lunak','kode' => 'IF106'],
        ])->map(fn ($mk) => MataKuliah::create([...$mk, 'user_id' => $admin->id]));

        // ── Jadwal (Senin–Jumat) ───────────────────────────────────────────
        $jadwalData = [
            ['mata_kuliah_id' => $mataKuliahs[0]->id, 'kelas_id' => $kelas['IF-01']->id, 'hari' => 'senin',  'jam_mulai' => '08:00', 'jam_selesai' => '09:40'],
            ['mata_kuliah_id' => $mataKuliahs[1]->id, 'kelas_id' => $kelas['IF-01']->id, 'hari' => 'selasa', 'jam_mulai' => '10:30', 'jam_selesai' => '12:10'],
            ['mata_kuliah_id' => $mataKuliahs[2]->id, 'kelas_id' => $kelas['IF-01']->id, 'hari' => 'rabu',   'jam_mulai' => '13:00', 'jam_selesai' => '14:40'],
            ['mata_kuliah_id' => $mataKuliahs[3]->id, 'kelas_id' => $kelas['IF-02']->id, 'hari' => 'kamis',  'jam_mulai' => '08:00', 'jam_selesai' => '09:40'],
            ['mata_kuliah_id' => $mataKuliahs[4]->id, 'kelas_id' => $kelas['IF-02']->id, 'hari' => 'jumat',  'jam_mulai' => '10:30', 'jam_selesai' => '12:10'],
            ['mata_kuliah_id' => $mataKuliahs[5]->id, 'kelas_id' => $kelas['SI-01']->id, 'hari' => 'senin',  'jam_mulai' => '13:00', 'jam_selesai' => '14:40'],
        ];
        foreach ($jadwalData as $j) {
            Jadwal::create($j);
        }

        // ── Mahasiswa ──────────────────────────────────────────────────────
        $mahasiswaData = [
            ['name' => 'Neilsya Putri',    'email' => 'neilsyaputri@student.telkom.ac.id',  'nim' => '1030421310048', 'no_hp' => '085XXXXX', 'prodi' => 'Teknik Informatika', 'kelas' => 'IF-02'],
            ['name' => 'Rivaldo Tandoko',  'email' => 'rivaldo@student.telkom.ac.id',        'nim' => '1034217006',    'no_hp' => '085XXXXX', 'prodi' => 'Teknik Informatika', 'kelas' => 'IF-02'],
            ['name' => 'Wahyu Pratama',    'email' => 'wahyupratama@student.telkom.ac.id',   'nim' => '1034217008',    'no_hp' => '085XXXXX', 'prodi' => 'Teknik Informatika', 'kelas' => 'IF-02'],
            ['name' => 'Aiqbal Hermawan', 'email' => 'aiqbal@student.telkom.ac.id',         'nim' => '1034219030',    'no_hp' => '085XXXXX', 'prodi' => 'Teknik Informatika', 'kelas' => 'IF-02'],
            ['name' => 'Wahyu Argo Mulyo', 'email' => 'wahyuargomu123@gmail.com',            'nim' => '1034219045',    'no_hp' => '085XXXXX', 'prodi' => 'Sistem Informasi',   'kelas' => 'SI-01'],
            ['name' => 'Muhammad Farhan',  'email' => 'farhan@student.telkom.ac.id',         'nim' => '1034221008',    'no_hp' => '085XXXXX', 'prodi' => 'Teknik Informatika', 'kelas' => 'IF-01'],
        ];

        $mahasiswaModels = [];
        foreach ($mahasiswaData as $data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make('password'),
                'role'     => 'mahasiswa',
            ]);

            $mahasiswaModels[] = Mahasiswa::create([
                'user_id'  => $user->id,
                'nim'      => $data['nim'],
                'no_hp'    => $data['no_hp'],
                'prodi_id' => $prodis[$data['prodi']]->id,
                'kelas_id' => $kelas[$data['kelas']]->id,
            ]);
        }

        // ── Sesi & Absensi (histori 5 sesi terakhir) ──────────────────────
        $statusOptions = ['hadir', 'hadir', 'hadir', 'hadir', 'izin', 'alpha'];

        for ($i = 5; $i >= 1; $i--) {
            $tanggal = now()->subDays($i);
            $mk      = $mataKuliahs[array_rand($mataKuliahs->toArray())];

            $sesi = Sesi::create([
                'mata_kuliah_id' => $mk->id,
                'kelas_id'       => $kelas['IF-02']->id,
                'user_id'        => $admin->id,
                'token'          => strtoupper(Str::random(8)),
                'qr_data'        => Str::uuid(),
                'durasi'         => 90,
                'status'         => 'selesai',
                'started_at'     => $tanggal->setTime(8, 0),
                'ended_at'       => $tanggal->setTime(9, 30),
            ]);

            // Buat absensi untuk mahasiswa IF-02
            foreach (array_slice($mahasiswaModels, 0, 4) as $mhs) {
                Absensi::create([
                    'sesi_id'      => $sesi->id,
                    'mahasiswa_id' => $mhs->id,
                    'status'       => $statusOptions[array_rand($statusOptions)],
                    'waktu_scan'   => $tanggal->setTime(8, rand(1, 15)),
                ]);
            }
        }
    }
}
