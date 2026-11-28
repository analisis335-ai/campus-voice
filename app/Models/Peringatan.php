<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peringatan extends Model
{
    protected $table = 'peringatan';
    protected $primaryKey = 'id_peringatan';

    protected $fillable = [
        'id_admin',
        'id_pengguna_target',
        'tipe_target',
        'id_target',
        'pesan',
        'tanggal_kirim',
    ];

    protected $casts = [
        'tanggal_kirim' => 'datetime',
    ];

    public function targetPengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna_target', 'id_pengguna');
    }

    public function admin()
    {
        return $this->belongsTo(Pengguna::class, 'id_admin', 'id_pengguna');
    }

    public static function bolehHapusPaksa(string $tipe, int $idTarget): bool
    {
        $terakhir = static::where('tipe_target', $tipe)
            ->where('id_target', $idTarget)
            ->latest('tanggal_kirim')
            ->first();

        return $terakhir !== null && $terakhir->tanggal_kirim->lte(now()->subHour());
    }

    public static function hitungUntukPengguna(int $idPengguna): int
    {
        return static::where('id_pengguna_target', $idPengguna)->count();
    }
}
