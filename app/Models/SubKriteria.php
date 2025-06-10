<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKriteria extends Model
{
    // app/Models/SubKriteria.php
    protected $fillable = ['kriteria_id', 'nama', 'nilai', 'keterangan'];

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }
}
