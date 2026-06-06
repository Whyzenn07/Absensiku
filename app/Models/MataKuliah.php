<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliahs';

    protected $fillable = ['nama', 'kode', 'user_id'];

    public function dosen()
    {
        return $this->belongsTo(User::class, 'user_id');
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
