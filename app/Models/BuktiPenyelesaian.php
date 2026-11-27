<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuktiPenyelesaian extends Model
{
    protected $table = 'bukti_penyelesaian';
    protected $primaryKey = 'id_bukti';

    protected $fillable = [
        'id_postingan',
        'id_petugas',
        'nama_file',
        'path_file',
        'deskripsi',
        'tanggal_unggah',
    ];

    protected $casts = [
        'tanggal_unggah' => 'datetime',
    ];

    public function petugas()
    {
        return $this->belongsTo(Pengguna::class, 'id_petugas', 'id_pengguna');
    }
}
