<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $fillable = ['nama', 'bobot', 'jenis', 'penilai'];

    public function subKriterias()
    {
        return $this->hasMany(SubKriteria::class);
    }

    public function penilaians()
    {
        return $this->hasMany(Penilaian::class);
    }

    public function scopeForPenilai($query, $role)
    {
        return $query->where('penilai', 'semua')
            ->orWhere('penilai', $role);
    }
}
