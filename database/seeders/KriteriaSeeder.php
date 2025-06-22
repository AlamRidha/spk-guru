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
        // Data untuk penilai kepsek
        Kriteria::create([
            'nama' => 'Moralitas',
            'bobot' => 2.0000,
            'jenis' => 'benefit',
            'penilai' => 'kepsek'
        ]);

        Kriteria::create([
            'nama' => 'Kedisiplinan',
            'bobot' => 3.0000,
            'jenis' => 'benefit',
            'penilai' => 'kepsek'
        ]);

        Kriteria::create([
            'nama' => 'Tanggung Jawab',
            'bobot' => 4.0000,
            'jenis' => 'benefit',
            'penilai' => 'kepsek'
        ]);

        Kriteria::create([
            'nama' => 'Implementasi Pembelajaran',
            'bobot' => 5.0000,
            'jenis' => 'benefit',
            'penilai' => 'kepsek'
        ]);

        // Data untuk penilai wakil_kurikulum
        Kriteria::create([
            'nama' => 'Pengalaman Mengajar',
            'bobot' => 2.0000,
            'jenis' => 'benefit',
            'penilai' => 'wakil_kurikulum'
        ]);

        Kriteria::create([
            'nama' => 'Tingkat Pendidikan',
            'bobot' => 3.0000,
            'jenis' => 'benefit',
            'penilai' => 'wakil_kurikulum'
        ]);

        Kriteria::create([
            'nama' => 'Jumlah Mata Pelajaran Yang Diajarkan',
            'bobot' => 4.0000,
            'jenis' => 'benefit',
            'penilai' => 'wakil_kurikulum'
        ]);

        Kriteria::create([
            'nama' => 'Frekuensi Mengajar Perminggu',
            'bobot' => 5.0000,
            'jenis' => 'benefit',
            'penilai' => 'wakil_kurikulum'
        ]);
    }
}
