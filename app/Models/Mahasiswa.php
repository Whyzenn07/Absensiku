<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nim',
        'no_hp',
        'prodi_id',
        'kelas_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    public function getPersentaseKehadiranAttribute(): float
    {
        $total = $this->absensis()->count();
        if ($total === 0) return 0;

        $hadir = $this->absensis()->where('status', 'hadir')->count();
        return round(($hadir / $total) * 100);
    }

    public function getInisialsAttribute(): string
    {
        $words = explode(' ', $this->user->name);
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper($word[0]);
        }
        return $initials;
    }
}
