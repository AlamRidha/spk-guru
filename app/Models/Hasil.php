<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hasil extends Model
{
    protected $fillable = ['guru_id', 'nilai_optimasi', 'ranking', 'tahun_penilaian'];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
