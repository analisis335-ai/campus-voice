@extends('tata_letak.polos')
@section('judul', 'Campus Voice — Selamat Datang')

@section('konten')
<section class="kartu kartu-kecil" style="text-align: center;">
  <span class="lencana-peran">Portal Aspirasi Kampus</span>
  <h1 class="jarak-atas">Campus Voice</h1>
  <p class="redup jarak-bawah">
    Sistem informasi aspirasi mahasiswa berbasis web. Sampaikan kritik, saran, dan pantau tindak lanjut dari pihak kampus dalam satu linimasa.
  </p>
  <div class="baris-fleks" style="justify-content: center;">
    <a href="{{ route('daftar') }}" class="tombol tombol-utama">Daftar</a>
    <a href="{{ route('masuk') }}" class="tombol">Masuk</a>
  </div>
</section>
@endsection
