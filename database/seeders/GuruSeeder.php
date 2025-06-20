<?php

namespace Database\Seeders;

use App\Models\Guru;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataGuru = [
            ['nama' => 'Dodo Dikando, Amd', 'jabatan' => 'Guru Matematika'],
            ['nama' => 'Supriatno', 'jabatan' => 'Guru IPA'],
            ['nama' => 'Fiska Wahyuni, M.pd', 'jabatan' => 'Guru Bahasa Indonesia'],
            ['nama' => 'Zulfadli', 'jabatan' => 'Guru Bahasa Inggris'],
            ['nama' => 'Rika Yunita, S.pd', 'jabatan' => 'Guru IPS'],
            ['nama' => 'Rita Hartini, S.pd', 'jabatan' => 'Guru PPKN'],
            ['nama' => 'Habiburrahman, S.pd', 'jabatan' => 'Guru Agama'],
            ['nama' => 'Deni Candra, S.pd', 'jabatan' => 'Guru Seni Budaya'],
            ['nama' => 'Lukman Indrayani, S.ak', 'jabatan' => 'Guru Ekonomi'],
        ];

        foreach ($dataGuru as $key => $guru) {
            Guru::create([
                'nama' => $guru['nama'],
                'nip' => $this->generateNIP($key + 1), // NIP otomatis
                'jabatan' => $guru['jabatan']
            ]);
        }
    }

    private function generateNIP($sequence): string
    {
        $datePart = date('Ymd');
        $sequencePart = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        return $datePart . $sequencePart;
    }
}
