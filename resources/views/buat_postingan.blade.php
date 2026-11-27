@extends('tata_letak.utama')
@section('judul', 'Buat Postingan — Campus Voice')

@section('konten')
<section class="kartu">
  <h1>Buat Postingan</h1>
  <p class="redup jarak-bawah">Sampaikan aspirasi, saran, atau keluhan lingkungan kampus. Isi postingan tidak dapat diubah setelah dikirim.</p>

  <form action="{{ route('postingan.simpan') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="grup-input">
      <label for="postingan-deskripsi">Deskripsi Postingan</label>
      <textarea id="postingan-deskripsi" name="deskripsi" placeholder="Tulis aspirasi Anda dengan jelas..." required>{{ old('deskripsi') }}</textarea>
    </div>

    <div class="grup-input">
      <label for="postingan-gambar">Lampiran Gambar (Opsional, JPG/PNG/WEBP maks. 2 MB)</label>
      <input type="file" id="postingan-gambar" name="gambar_file" accept=".jpg,.jpeg,.png,.webp">
    </div>

    <div class="baris-fleks">
      <button type="submit" class="tombol tombol-utama">Kirim Postingan</button>
      <a href="{{ route('dasbor') }}" class="tombol">Batal</a>
    </div>
  </form>
</section>
@endsection
