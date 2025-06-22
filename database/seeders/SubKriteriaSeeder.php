<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use App\Models\SubKriteria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubKriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data sub kriteria untuk penilai kepsek
        $this->createSubKriteriaForKepsek();

        // Data sub kriteria untuk penilai wakil kurikulum
        $this->createSubKriteriaForWakilKurikulum();
    }

    private function createSubKriteriaForKepsek(): void
    {
        $kriteriaKepsek = [
            'Moralitas',
            'Kedisiplinan',
            'Tanggung Jawab',
            'Implementasi Pembelajaran'
        ];

        $subKriteriaData = [
            ['81-100', 5, 'Sangat Baik'],
            ['61-80', 4, 'Baik'],
            ['41-60', 3, 'Cukup'],
            ['21-40', 2, 'Kurang'],
            ['0-20', 1, 'Sangat Kurang'],
        ];

        foreach ($kriteriaKepsek as $namaKriteria) {
            $kriteria = Kriteria::where('nama', $namaKriteria)->first();

            foreach ($subKriteriaData as $data) {
                SubKriteria::create([
                    'kriteria_id' => $kriteria->id,
                    'nama' => $data[0],
                    'nilai' => $data[1],
                    'keterangan' => $data[2],
                ]);
            }
        }
    }

    private function createSubKriteriaForWakilKurikulum(): void
    {
        // Data untuk Pengalaman Mengajar
        $pengalamanMengajar = Kriteria::where('nama', 'Pengalaman Mengajar')->first();
        $this->createSubKriteria($pengalamanMengajar->id, [
            ['>5 Tahun', 5, 'Sangat Baik'],
            ['4-5 Tahun', 4, 'Baik'],
            ['2-3 Tahun', 3, 'Cukup'],
            ['<= 1 Tahun', 2, 'Buruk'],
        ]);

        // Data untuk Tingkat Pendidikan
        $tingkatPendidikan = Kriteria::where('nama', 'Tingkat Pendidikan')->first();
        $this->createSubKriteria($tingkatPendidikan->id, [
            ['S2', 5, 'Sangat Baik'],
            ['S1', 4, 'Baik'],
            ['D3', 3, 'Cukup'],
            ['SMA/SMK', 2, 'Buruk'],
        ]);

        // Data untuk Jumlah Mata Pelajaran Yang Diajarkan
        $jumlahMapel = Kriteria::where('nama', 'Jumlah Mata Pelajaran Yang Diajarkan')->first();
        $this->createSubKriteria($jumlahMapel->id, [
            ['>6 Mapel', 5, 'Sangat Baik'],
            ['5 Mapel', 4, 'Baik'],
            ['3-4 Mapel', 3, 'Cukup'],
            ['1-2 Mapel', 2, 'Buruk'],
        ]);

        // Data untuk Frekuensi Mengajar Perminggu
        $frekuensiMengajar = Kriteria::where('nama', 'Frekuensi Mengajar Perminggu')->first();
        $this->createSubKriteria($frekuensiMengajar->id, [
            ['Setiap Hari', 5, 'Sangat Baik'],
            ['5 Hari', 4, 'Baik'],
            ['4 Hari', 3, 'Cukup'],
            ['2-3 hari', 2, 'Buruk'],
            ['1 Hari', 1, 'Sangat Buruk'],
        ]);
    }

    private function createSubKriteria($kriteriaId, array $subKriteriaData): void
    {
        foreach ($subKriteriaData as $data) {
            SubKriteria::create([
                'kriteria_id' => $kriteriaId,
                'nama' => $data[0],
                'nilai' => $data[1],
                'keterangan' => $data[2],
            ]);
        }
    }
}
