<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postingan extends Model
{
    protected $table = 'postingan';
    protected $primaryKey = 'id_postingan';

    protected $fillable = [
        'id_penulis',
        'deskripsi',
        'gambar_file',
        'tanggal_post',
        'status',
        'tanggal_hapus',
    ];

    protected $casts = [
        'tanggal_post' => 'datetime',
        'tanggal_hapus' => 'datetime',
    ];

    public function penulis()
    {
        return $this->belongsTo(Pengguna::class, 'id_penulis', 'id_pengguna');
    }

    public function komentar()
    {
        return $this->hasMany(Komentar::class, 'id_postingan', 'id_postingan');
    }

    public function suka()
    {
        return $this->hasMany(Suka::class, 'id_postingan', 'id_postingan');
    }

    public function buktiPenyelesaian()
    {
        return $this->hasMany(BuktiPenyelesaian::class, 'id_postingan', 'id_postingan');
    }

    public function sudahDisukaiOleh(int $idPengguna): bool
    {
        return $this->suka->contains('id_pengguna', $idPengguna);
    }

    public function bisaDihapusPaksa(): bool
    {
        return $this->status === 'Aktif' && Peringatan::bolehHapusPaksa('postingan', $this->id_postingan);
    }

    public function bisaDipulihkan(): bool
    {
        return $this->status === 'Dihapus'
            && $this->tanggal_hapus !== null
            && $this->tanggal_hapus->gt(now()->subDay());
    }
}
