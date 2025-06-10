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
        $gurus = [
            ['nama' => 'Dodo Dikando, Amd', 'nip' => '123', 'jabatan' => 'Guru TKJ'],
            ['nama' => 'Supriatno', 'nip' => '124', 'jabatan' => 'Guru Matematika'],

        ];

        Guru::insert($gurus);
    }
}
