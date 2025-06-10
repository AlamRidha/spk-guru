<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{

    protected $fillable = ['user_id', 'guru_id', 'kriteria_id', 'nilai'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }
}
