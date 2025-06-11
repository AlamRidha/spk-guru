<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $fillable = ['nama', 'nip', 'jabatan'];

    protected $casts = [
        'nip' => 'string', // Pastikan NIP selalu sebagai string
    ];

    // Mutator untuk memastikan nama disimpan apa adanya
    public function setNamaAttribute($value)
    {
        $this->attributes['nama'] = trim($value);
    }

    // Mutator untuk NIP
    public function setNipAttribute($value)
    {
        $this->attributes['nip'] = $value ? trim($value) : null;
    }

    // Accessor untuk NIP
    public function getNipAttribute($value)
    {
        return $value ?: '-';
    }

    public function penilaians()
    {
        return $this->hasMany(Penilaian::class);
    }

    public function hasil()
    {
        return $this->hasOne(Hasil::class);
    }
}
