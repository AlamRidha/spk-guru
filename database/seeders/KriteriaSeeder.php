<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kriteria::create([
            'nama' => 'Moralitas',
            'bobot' => 2.0000,
            'jenis' => 'benefit',
            'penilai' => 'kepsek'
        ]);

        // Kriteria untuk Wakil Kurikulum
        Kriteria::create([
            'nama' => 'Pengalaman Mengajar',
            'bobot' => 2.0000,
            'jenis' => 'benefit',
            'penilai' => 'wakil_kurikulum'
        ]);
    }
}
