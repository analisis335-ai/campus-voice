@extends('tata_letak.polos')
@section('judul', 'Masuk — Campus Voice')

@section('konten')
<section class="kartu kartu-kecil">
  <h1>Masuk Akun</h1>
  <p class="redup jarak-bawah">Gunakan NIM/NIP dan password yang terdaftar.</p>

  @if (session('sukses'))
    <div class="pesan pesan-sukses">{{ session('sukses') }}</div>
  @endif
  @if (session('galat'))
    <div class="pesan pesan-bahaya">{{ session('galat') }}</div>
  @endif
  @if ($errors->any())
    <div class="pesan pesan-bahaya">{{ $errors->first() }}</div>
  @endif

  <form action="{{ route('masuk.proses') }}" method="POST">
    @csrf
    <div class="grup-input">
      <label for="masuk-nim">NIM / NIP</label>
      <input type="text" id="masuk-nim" name="nim_nip" value="{{ old('nim_nip') }}" placeholder="Masukkan NIM atau NIP" required>
    </div>

    <div class="grup-input">
      <label for="masuk-sandi">Password</label>
      <input type="password" id="masuk-sandi" name="kata_sandi" placeholder="Masukkan password" required>
    </div>

    <button type="submit" class="tombol tombol-utama tombol-penuh">Masuk</button>
  </form>

  <p class="teks-kecil redup jarak-atas" style="text-align: center;">
    Belum punya akun? <a href="{{ route('daftar') }}">Daftar di sini</a> &middot; <a href="{{ route('awal') }}">Kembali</a>
  </p>
</section>
@endsection
