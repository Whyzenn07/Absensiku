<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('user_id')->comment('dosen/admin yang membuat')->constrained()->onDelete('cascade');
            $table->string('token', 8)->unique();
            $table->text('qr_data');
            $table->unsignedSmallInteger('durasi')->comment('durasi dalam menit');
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesis');
    }
};
