<?php

namespace Database\Seeders;

use App\Models\BuktiPenyelesaian;
use App\Models\Komentar;
use App\Models\Pengguna;
use App\Models\Peringatan;
use App\Models\Postingan;
use App\Models\Suka;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $budi = Pengguna::create([
            'nama' => 'Budi Santoso',
            'nim_nip' => '230101001',
            'peran' => 'Mahasiswa',
            'file_bukti' => 'contoh_ktm_budi.jpg',
            'kata_sandi' => Hash::make('123456'),
            'status' => 'Aktif',
        ]);

        $siti = Pengguna::create([
            'nama' => 'Siti Aminah',
            'nim_nip' => '1989041201',
            'peran' => 'Pegawai',
            'file_bukti' => 'contoh_sk_siti.pdf',
            'kata_sandi' => Hash::make('123456'),
            'status' => 'Aktif',
        ]);

        $rizky = Pengguna::create([
            'nama' => 'Rizky Pratama',
            'nim_nip' => '230101002',
            'peran' => 'Mahasiswa',
            'file_bukti' => 'contoh_ktm_rizky.jpg',
            'kata_sandi' => Hash::make('123456'),
            'status' => 'Aktif',
        ]);

        $admin = Pengguna::create([
            'nama' => 'Admin Kampus',
            'nim_nip' => 'ADMIN01',
            'peran' => 'Admin',
            'file_bukti' => '-',
            'kata_sandi' => Hash::make('admin123'),
            'status' => 'Aktif',
        ]);

        $post1 = Postingan::create([
            'id_penulis' => $budi->id_pengguna,
            'deskripsi' => 'AC di ruang kuliah B-204 tidak dingin sejak kemarin dan proyektor sering berkedip saat perkuliahan berlangsung. Mohon tindak lanjut bagian sarana prasarana.',
            'tanggal_post' => now()->subHours(3),
            'status' => 'Aktif',
        ]);

        $post2 = Postingan::create([
            'id_penulis' => $rizky->id_pengguna,
            'deskripsi' => 'Contoh postingan yang melanggar tata tertib diskusi kampus (sudah diperingatkan > 1 jam lalu untuk uji hapus paksa admin).',
            'tanggal_post' => now()->subHours(2),
            'status' => 'Aktif',
        ]);

        $post3 = Postingan::create([
            'id_penulis' => $rizky->id_pengguna,
            'deskripsi' => 'Postingan lama yang sudah dihapus paksa 2 jam lalu dan masih dapat dipulihkan admin (< 1 hari).',
            'tanggal_post' => now()->subHours(5),
            'status' => 'Dihapus',
            'tanggal_hapus' => now()->subHours(2),
        ]);

        Komentar::create([
            'id_postingan' => $post1->id_postingan,
            'id_penulis' => $siti->id_pengguna,
            'isi' => 'Terima kasih laporannya. Teknisi sudah kami tugaskan memeriksa unit AC dan kabel proyektor hari ini.',
            'tanggal_komentar' => now()->subHours(2),
            'status' => 'Aktif',
        ]);

        Suka::create([
            'id_postingan' => $post1->id_postingan,
            'id_pengguna' => $siti->id_pengguna,
            'tanggal_suka' => now()->subHours(2),
        ]);

        Suka::create([
            'id_postingan' => $post1->id_postingan,
            'id_pengguna' => $rizky->id_pengguna,
            'tanggal_suka' => now()->subHour(),
        ]);

        BuktiPenyelesaian::create([
            'id_postingan' => $post1->id_postingan,
            'id_petugas' => $siti->id_pengguna,
            'nama_file' => 'berita_acara_perbaikan_b204.pdf',
            'path_file' => 'contoh/berita_acara_perbaikan_b204.pdf',
            'deskripsi' => 'AC telah dibersihkan dan kabel HDMI proyektor Ruang B-204 telah diganti.',
            'tanggal_unggah' => now()->subHour(),
        ]);

        Peringatan::create([
            'id_admin' => $admin->id_pengguna,
            'id_pengguna_target' => $rizky->id_pengguna,
            'tipe_target' => 'postingan',
            'id_target' => $post3->id_postingan,
            'pesan' => 'Mohon gunakan bahasa yang sopan dan sesuai topik aspirasi kampus.',
            'tanggal_kirim' => now()->subHours(4),
        ]);

        Peringatan::create([
            'id_admin' => $admin->id_pengguna,
            'id_pengguna_target' => $rizky->id_pengguna,
            'tipe_target' => 'komentar',
            'id_target' => 99,
            'pesan' => 'Hindari komentar yang menyerang pihak lain secara personal.',
            'tanggal_kirim' => now()->subHours(3),
        ]);

        Peringatan::create([
            'id_admin' => $admin->id_pengguna,
            'id_pengguna_target' => $rizky->id_pengguna,
            'tipe_target' => 'postingan',
            'id_target' => $post2->id_postingan,
            'pesan' => 'Postingan tidak sesuai ketentuan forum. Harap hapus dalam 1 jam.',
            'tanggal_kirim' => now()->subMinutes(90),
        ]);
    }
}
