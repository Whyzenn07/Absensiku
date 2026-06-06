<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensis';

    protected $fillable = ['sesi_id', 'mahasiswa_id', 'status', 'waktu_scan'];

    protected $casts = [
        'waktu_scan' => 'datetime',
    ];

    public function sesi()
    {
        return $this->belongsTo(Sesi::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
