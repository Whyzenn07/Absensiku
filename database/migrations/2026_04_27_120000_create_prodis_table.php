<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prodis', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        DB::table('prodis')->insert([
            ['nama' => 'Teknik Informatika', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Sistem Informasi', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Teknik Elektro', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Manajemen Informatika', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('prodis');
    }
};
