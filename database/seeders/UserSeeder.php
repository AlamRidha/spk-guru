<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nama' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',

        ]);

        User::create([
            'nama' => 'Susanti, A.Md',
            'email' => 'kepsek@gmail.com',
            'password' => bcrypt('kepsek123'),
            'role' => 'kepsek',
        ]);
    }
}
