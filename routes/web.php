<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AutentikasiController;
use App\Http\Controllers\DasborController;
use Illuminate\Support\Facades\Route;

// Halaman Publik & Autentikasi Pengguna
Route::get('/', [AutentikasiController::class, 'halamanAwal'])->name('awal');
Route::get('/daftar', [AutentikasiController::class, 'halamanDaftar'])->name('daftar');
Route::post('/daftar', [AutentikasiController::class, 'prosesDaftar'])->name('daftar.proses');
Route::get('/masuk', [AutentikasiController::class, 'halamanMasuk'])->name('masuk');
Route::post('/masuk', [AutentikasiController::class, 'prosesMasuk'])->name('masuk.proses');

// Halaman Publik & Autentikasi Admin
Route::get('/admin', [AutentikasiController::class, 'halamanAwalAdmin'])->name('admin.awal');
Route::get('/admin/masuk', [AutentikasiController::class, 'halamanMasukAdmin'])->name('admin.masuk');
Route::post('/admin/masuk', [AutentikasiController::class, 'prosesMasukAdmin'])->name('admin.masuk.proses');

// Keluar (Semua Peran)
Route::post('/keluar', [AutentikasiController::class, 'keluar'])->middleware('peran:Mahasiswa,Pegawai,Admin')->name('keluar');

// Fitur Pengguna (Mahasiswa & Pegawai)
Route::middleware('peran:Mahasiswa,Pegawai')->group(function () {
    Route::get('/dasbor', [DasborController::class, 'dasbor'])->name('dasbor');
    Route::get('/buat-postingan', [DasborController::class, 'halamanBuatPostingan'])->name('postingan.buat');
    Route::post('/buat-postingan', [DasborController::class, 'simpanPostingan'])->name('postingan.simpan');
    Route::get('/akun', [DasborController::class, 'akun'])->name('akun');
    Route::get('/kotak-masuk', [DasborController::class, 'kotakMasuk'])->name('kotak_masuk');
    Route::post('/postingan/{id}/hapus', [DasborController::class, 'hapusPostinganSendiri'])->name('postingan.hapus_sendiri');
    Route::post('/komentar/{id}/hapus', [DasborController::class, 'hapusKomentarSendiri'])->name('komentar.hapus_sendiri');
    Route::get('/pengaturan', [DasborController::class, 'pengaturan'])->name('pengaturan');
});

// Fitur Khusus Pegawai / Tendik
Route::post('/postingan/{id}/bukti', [DasborController::class, 'simpanBukti'])
    ->middleware('peran:Pegawai')
    ->name('postingan.bukti');

// Fitur Bersama Semua Aktor Terautentikasi (Like, Komentar, Ubah Sandi, Bukti Akun)
Route::middleware('peran:Mahasiswa,Pegawai,Admin')->group(function () {
    Route::post('/postingan/{id}/suka', [DasborController::class, 'toggleSuka'])->name('postingan.suka');
    Route::post('/postingan/{id}/komentar', [DasborController::class, 'simpanKomentar'])->name('postingan.komentar');
    Route::post('/pengaturan/sandi', [DasborController::class, 'ubahSandi'])->name('pengaturan.sandi');
    Route::get('/bukti-akun/{id}', [DasborController::class, 'buktiAkun'])->name('akun.bukti');
});

// Fitur Khusus Admin
Route::prefix('admin')->name('admin.')->middleware('peran:Admin')->group(function () {
    Route::get('/dasbor', [DasborController::class, 'dasbor'])->name('dasbor');
    Route::get('/peringatan/{tipe}/{id}', [AdminController::class, 'halamanPeringatan'])->name('peringatan');
    Route::post('/peringatan/{tipe}/{id}', [AdminController::class, 'kirimPeringatan'])->name('peringatan.kirim');
    Route::get('/hapus/{tipe}/{id}', [AdminController::class, 'halamanHapus'])->name('hapus');
    Route::post('/hapus/{tipe}/{id}', [AdminController::class, 'hapusPaksa'])->name('hapus.paksa');
    Route::post('/blokir/{id}', [AdminController::class, 'blokirAkun'])->name('blokir');
    Route::get('/kotak-masuk', [AdminController::class, 'kotakMasuk'])->name('kotak_masuk');
    Route::post('/pulihkan/{tipe}/{id}', [AdminController::class, 'pulihkan'])->name('pulihkan');
    Route::post('/buka-blokir/{id}', [AdminController::class, 'bukaBlokir'])->name('buka_blokir');
    Route::get('/pengaturan', [DasborController::class, 'pengaturan'])->name('pengaturan');
});
