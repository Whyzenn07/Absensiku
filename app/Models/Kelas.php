<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $fillable = ['nama'];

    public function mahasiswas()
    {
        return $this->hasMany(Mahasiswa::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function sesis()
    {
        return $this->hasMany(Sesi::class);
    }
}
