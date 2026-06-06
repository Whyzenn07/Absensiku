<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sesi extends Model
{
    protected $fillable = [
        'mata_kuliah_id',
        'kelas_id',
        'user_id',
        'token',
        'qr_data',
        'durasi',
        'status',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif' && now()->lt($this->started_at->addMinutes($this->durasi));
    }

    public function jumlahHadir(): int
    {
        return $this->absensis()->where('status', 'hadir')->count();
    }
}
