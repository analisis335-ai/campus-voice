<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id('id_pengguna');
            $table->string('nama');
            $table->string('nim_nip', 50)->unique();
            $table->enum('peran', ['Mahasiswa', 'Pegawai', 'Admin']);
            $table->string('file_bukti')->default('-');
            $table->string('kata_sandi');
            $table->enum('status', ['Aktif', 'Diblokir'])->default('Aktif');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('postingan', function (Blueprint $table) {
            $table->id('id_postingan');
            $table->foreignId('id_penulis')->constrained('pengguna', 'id_pengguna')->cascadeOnDelete();
            $table->text('deskripsi');
            $table->string('gambar_file')->nullable();
            $table->timestamp('tanggal_post')->useCurrent();
            $table->enum('status', ['Aktif', 'Dihapus'])->default('Aktif');
            $table->timestamp('tanggal_hapus')->nullable();
            $table->timestamps();
        });

        Schema::create('komentar', function (Blueprint $table) {
            $table->id('id_komentar');
            $table->foreignId('id_postingan')->constrained('postingan', 'id_postingan')->cascadeOnDelete();
            $table->foreignId('id_penulis')->constrained('pengguna', 'id_pengguna')->cascadeOnDelete();
            $table->text('isi');
            $table->timestamp('tanggal_komentar')->useCurrent();
            $table->enum('status', ['Aktif', 'Dihapus'])->default('Aktif');
            $table->timestamp('tanggal_hapus')->nullable();
            $table->timestamps();
        });

        Schema::create('suka', function (Blueprint $table) {
            $table->id('id_suka');
            $table->foreignId('id_postingan')->constrained('postingan', 'id_postingan')->cascadeOnDelete();
            $table->foreignId('id_pengguna')->constrained('pengguna', 'id_pengguna')->cascadeOnDelete();
            $table->timestamp('tanggal_suka')->useCurrent();
            $table->unique(['id_postingan', 'id_pengguna']);
        });

        Schema::create('bukti_penyelesaian', function (Blueprint $table) {
            $table->id('id_bukti');
            $table->foreignId('id_postingan')->constrained('postingan', 'id_postingan')->cascadeOnDelete();
            $table->foreignId('id_petugas')->constrained('pengguna', 'id_pengguna')->cascadeOnDelete();
            $table->string('nama_file');
            $table->string('path_file');
            $table->text('deskripsi');
            $table->timestamp('tanggal_unggah')->useCurrent();
            $table->timestamps();
        });

        Schema::create('peringatan', function (Blueprint $table) {
            $table->id('id_peringatan');
            $table->foreignId('id_admin')->constrained('pengguna', 'id_pengguna')->cascadeOnDelete();
            $table->foreignId('id_pengguna_target')->constrained('pengguna', 'id_pengguna')->cascadeOnDelete();
            $table->enum('tipe_target', ['postingan', 'komentar']);
            $table->unsignedBigInteger('id_target');
            $table->text('pesan');
            $table->timestamp('tanggal_kirim')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peringatan');
        Schema::dropIfExists('bukti_penyelesaian');
        Schema::dropIfExists('suka');
        Schema::dropIfExists('komentar');
        Schema::dropIfExists('postingan');
        Schema::dropIfExists('pengguna');
    }
};
