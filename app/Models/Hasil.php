<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hasil extends Model
{
    protected $fillable = [
        'guru_id',
        'penilai_id',
        'nilai_optimasi',
        'ranking',
        'tahun_penilaian',
        'jenis_penilai'
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }
}
