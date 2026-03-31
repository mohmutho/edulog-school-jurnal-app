<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel Jadwal (Schedules)
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            
            // Data Pelaksanaan Jurnal
            $table->date('date'); // Tanggal aktual kelas berjalan
            $table->text('activity_type'); // Jenis kegiatan yang dilakukan
            $table->text('description')->nullable(); // Rincian materi yang diajarkan
            $table->text('notes')->nullable(); // Catatan khusus (Opsional)
            
            // Flag keamanan untuk fitur Bulk Action Admin (Classmeeting/Libur)
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
