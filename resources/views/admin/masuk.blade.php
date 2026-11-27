@extends('tata_letak.polos')
@section('judul', 'Masuk Admin — Campus Voice')

@section('konten')
<section class="kartu kartu-kecil">
  <span class="lencana-peran lencana-admin">Admin</span>
  <h1 class="jarak-atas">Masuk sebagai Admin</h1>
  <p class="redup jarak-bawah">Masukkan nama admin dan password.</p>

  @if (session('galat'))
    <div class="pesan pesan-bahaya">{{ session('galat') }}</div>
  @endif
  @if ($errors->any())
    <div class="pesan pesan-bahaya">{{ $errors->first() }}</div>
  @endif

  <form action="{{ route('admin.masuk.proses') }}" method="POST">
    @csrf
    <div class="grup-input">
      <label for="admin-nama">Nama Admin</label>
      <input type="text" id="admin-nama" name="nama" value="{{ old('nama') }}" placeholder="Masukkan nama admin" required>
    </div>

    <div class="grup-input">
      <label for="admin-sandi">Password</label>
      <input type="password" id="admin-sandi" name="kata_sandi" placeholder="Masukkan password" required>
    </div>

    <button type="submit" class="tombol tombol-utama tombol-penuh">Masuk</button>
  </form>

  <p class="teks-kecil redup jarak-atas" style="text-align: center;">
    <a href="{{ route('admin.awal') }}">Kembali</a>
  </p>
</section>
@endsection
