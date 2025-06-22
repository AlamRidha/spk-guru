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
        Schema::create('hasils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('penilai_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('nilai_optimasi', 10, 6);
            $table->integer('ranking')->unsigned();
            $table->year('tahun_penilaian');
            $table->enum('jenis_penilai', ['kepsek', 'wakil_kurikulum']);
            $table->timestamps();

            // Unique constraint
            $table->unique(['guru_id', 'penilai_id', 'tahun_penilaian']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasils');
    }
};
