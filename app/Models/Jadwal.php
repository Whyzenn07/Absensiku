<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $fillable = ['mata_kuliah_id', 'kelas_id', 'hari', 'jam_mulai', 'jam_selesai'];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function isHariIni(): bool
    {
        $hariMap = [
            'senin'  => 'Monday',
            'selasa' => 'Tuesday',
            'rabu'   => 'Wednesday',
            'kamis'  => 'Thursday',
            'jumat'  => 'Friday',
            'sabtu'  => 'Saturday',
        ];

        return now()->format('l') === ($hariMap[$this->hari] ?? '');
    }
}
