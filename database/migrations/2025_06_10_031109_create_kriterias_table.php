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
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->decimal('bobot', 8, 4);
            $table->enum('jenis', ['benefit', 'cost']); // Benefit: makin tinggi makin baik
            $table->enum('penilai', ['kepsek', 'wakil_kurikulum', 'semua']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kriterias');
    }
};
