@extends('tata_letak.polos')
@section('judul', 'Admin — Campus Voice')

@section('konten')
<section class="kartu kartu-kecil" style="text-align: center;">
  <span class="lencana-peran lencana-admin">Portal Admin</span>
  <h1 class="jarak-atas">Campus Voice</h1>
  <p class="redup jarak-bawah">
    Selamat datang di halaman pengelolaan dan pengawasan aspirasi mahasiswa Campus Voice.
  </p>
  <a href="{{ route('admin.masuk') }}" class="tombol tombol-utama tombol-penuh">Masuk</a>
  <p class="teks-kecil redup jarak-atas">
    <a href="{{ route('awal') }}">Kembali ke Halaman Pengguna</a>
  </p>
</section>
@endsection
