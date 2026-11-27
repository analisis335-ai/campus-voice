<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{
    protected $table = 'komentar';
    protected $primaryKey = 'id_komentar';

    protected $fillable = [
        'id_postingan',
        'id_penulis',
        'isi',
        'tanggal_komentar',
        'status',
        'tanggal_hapus',
    ];

    protected $casts = [
        'tanggal_komentar' => 'datetime',
        'tanggal_hapus' => 'datetime',
    ];

    public function postingan()
    {
        return $this->belongsTo(Postingan::class, 'id_postingan', 'id_postingan');
    }

    public function penulis()
    {
        return $this->belongsTo(Pengguna::class, 'id_penulis', 'id_pengguna');
    }

    public function bisaDihapusPaksa(): bool
    {
        return $this->status === 'Aktif' && Peringatan::bolehHapusPaksa('komentar', $this->id_komentar);
    }

    public function bisaDipulihkan(): bool
    {
        return $this->status === 'Dihapus'
            && $this->tanggal_hapus !== null
            && $this->tanggal_hapus->gt(now()->subDay());
    }
}
