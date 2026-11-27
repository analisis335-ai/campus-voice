<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suka extends Model
{
    protected $table = 'suka';
    protected $primaryKey = 'id_suka';
    public $timestamps = false;

    protected $fillable = [
        'id_postingan',
        'id_pengguna',
        'tanggal_suka',
    ];

    protected $casts = [
        'tanggal_suka' => 'datetime',
    ];
}
