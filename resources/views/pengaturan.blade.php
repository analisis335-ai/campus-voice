@extends('tata_letak.utama')
@section('judul', 'Pengaturan — Campus Voice')

@section('konten')
<section class="kartu">
  <h1>Pengaturan Akun</h1>
  <p class="redup jarak-bawah">Ubah password akun atau kelola penghapusan konten Anda.</p>

  <form action="{{ route('pengaturan.sandi') }}" method="POST">
    @csrf
    <div class="grup-input">
      <label for="sandi-lama">Password Saat Ini</label>
      <input type="password" id="sandi-lama" name="sandi_lama" placeholder="Masukkan password saat ini" required>
    </div>

    <div class="grup-input">
      <label for="sandi-baru">Password Baru</label>
      <input type="password" id="sandi-baru" name="sandi_baru" placeholder="Minimal 6 karakter" required>
    </div>

    <div class="grup-input">
      <label for="sandi-konfirmasi">Konfirmasi Password Baru</label>
      <input type="password" id="sandi-konfirmasi" name="sandi_baru_confirmation" placeholder="Ulangi password baru" required>
    </div>

    <button type="submit" class="tombol tombol-utama">Simpan Password Baru</button>
  </form>
</section>

<section class="kartu">
  <h2>Hapus Postingan atau Komentar</h2>
  <p class="redup jarak-bawah">Untuk menghapus postingan atau komentar milik Anda, buka riwayat konten pada Kotak Masuk.</p>
  <a href="{{ route('kotak_masuk', ['tab' => 'postingan']) }}" class="tombol">Buka Kotak Masuk (Hapus Konten)</a>
</section>
@endsection
