<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        DB::table('kelas')->insert([
            ['nama' => 'IF-01', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'IF-02', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'SI-01', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'SI-02', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'TI-01', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'TI-02', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
