<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable
{
    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';

    protected $fillable = [
        'nama',
        'nim_nip',
        'peran',
        'file_bukti',
        'kata_sandi',
        'status',
    ];

    protected $hidden = [
        'kata_sandi',
        'remember_token',
    ];

    public function getAuthPassword(): string
    {
        return $this->kata_sandi;
    }

    public function postingan()
    {
        return $this->hasMany(Postingan::class, 'id_penulis', 'id_pengguna');
    }

    public function komentar()
    {
        return $this->hasMany(Komentar::class, 'id_penulis', 'id_pengguna');
    }

    public function peringatanDiterima()
    {
        return $this->hasMany(Peringatan::class, 'id_pengguna_target', 'id_pengguna');
    }
}
