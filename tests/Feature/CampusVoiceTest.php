<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Models\Postingan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CampusVoiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_halaman_publik_dapat_diakses(): void
    {
        $this->get('/')->assertStatus(200)->assertSee('Campus Voice');
        $this->get('/daftar')->assertStatus(200);
        $this->get('/masuk')->assertStatus(200);
        $this->get('/admin')->assertStatus(200);
        $this->get('/admin/masuk')->assertStatus(200);
    }

    public function test_alur_mahasiswa_dan_pegawai(): void
    {
        Storage::fake('public');

        $this->post('/masuk', ['nim_nip' => '230101001', 'kata_sandi' => '123456'])
            ->assertRedirect('/dasbor');

        $this->get('/dasbor')->assertStatus(200)->assertSee('AC di ruang kuliah B-204');
        $this->get('/akun')->assertStatus(200)->assertSee('Budi Santoso');
        $this->get('/kotak-masuk')->assertStatus(200);
        $this->get('/pengaturan')->assertStatus(200);

        $this->post('/buat-postingan', ['deskripsi' => 'Uji aspirasi baru mahasiswa'])
            ->assertRedirect('/dasbor');

        $postBaru = Postingan::where('deskripsi', 'Uji aspirasi baru mahasiswa')->firstOrFail();
        $this->post("/postingan/{$postBaru->id_postingan}/suka")->assertRedirect();
        $this->post("/postingan/{$postBaru->id_postingan}/komentar", ['isi' => 'Mendukung aspirasi ini'])->assertRedirect();
        $this->post('/keluar')->assertRedirect('/');

        $this->post('/masuk', ['nim_nip' => '1989041201', 'kata_sandi' => '123456'])
            ->assertRedirect('/dasbor');
        $this->post("/postingan/{$postBaru->id_postingan}/bukti", [
            'deskripsi' => 'Sudah ditindaklanjuti pegawai',
            'file_bukti' => UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf'),
        ])->assertRedirect();

        $this->get('/dasbor')->assertSee('Sudah ditindaklanjuti pegawai');
    }

    public function test_alur_moderasi_admin_1_jam_3_peringatan_dan_pemulihan(): void
    {
        $this->post('/admin/masuk', ['nama' => 'Admin Kampus', 'kata_sandi' => 'admin123'])
            ->assertRedirect('/admin/dasbor');

        $this->get('/admin/dasbor')->assertStatus(200);
        $this->get('/admin/hapus/postingan/2')->assertStatus(200)->assertSee('Hapus Secara Paksa');

        $this->post('/admin/hapus/postingan/2')->assertRedirect();
        $this->assertEquals('Dihapus', Postingan::find(2)->status);

        $rizky = Pengguna::where('nim_nip', '230101002')->firstOrFail();
        $this->post("/admin/blokir/{$rizky->id_pengguna}")->assertRedirect();
        $this->assertEquals('Diblokir', $rizky->fresh()->status);

        $this->post('/admin/pulihkan/postingan/2')->assertRedirect('/admin/kotak-masuk?tab=terhapus');
        $this->assertEquals('Aktif', Postingan::find(2)->status);

        $this->post("/admin/buka-blokir/{$rizky->id_pengguna}")->assertRedirect('/admin/kotak-masuk?tab=terblokir');
        $this->assertEquals('Aktif', $rizky->fresh()->status);

        $this->post('/keluar')->assertRedirect('/admin');
        $this->post('/masuk', ['nim_nip' => '230101002', 'kata_sandi' => '123456'])
            ->assertRedirect('/dasbor');
    }
}
