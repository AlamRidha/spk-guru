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
        $kriterias = [
            [
                'nama' => 'Pengalaman Mengajar',
                'bobot' => 0.1429,
                'jenis' => 'benefit',
                'sub_kriterias' => [
                    ['nama' => '>5 Tahun', 'nilai' => 5, 'keterangan' => 'Sangat Baik'],
                    ['nama' => '4-5 Tahun', 'nilai' => 4, 'keterangan' => 'Baik'],
                    ['nama' => '2-3 Tahun', 'nilai' => 3, 'keterangan' => 'Cukup'],
                    ['nama' => '<=1 Tahun', 'nilai' => 2, 'keterangan' => 'Buruk']
                ]
            ],
        ];

        foreach ($kriterias as $data) {
            $kriteria = Kriteria::create(Arr::except($data, ['sub_kriterias']));
            $kriteria->subKriterias()->createMany($data['sub_kriterias']);
        }
    }
}
