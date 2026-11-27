@extends('tata_letak.utama')
@section('judul', 'Akun Saya — Campus Voice')

@section('konten')
<section class="kartu">
  <div class="sebar jarak-bawah">
    <h1>Data Akun</h1>
    <a href="{{ route('pengaturan') }}" class="tombol">Pengaturan</a>
  </div>

  <div class="daftar-baris">
    <strong>Nama Lengkap</strong><span>{{ $pengguna->nama }}</span>
    <strong>NIM / NIP</strong><span>{{ $pengguna->nim_nip }}</span>
    <strong>Peran</strong><span>{{ $pengguna->peran }}</span>
    <strong>Status Akun</strong><span>{{ $pengguna->status }}</span>
    <strong>Bukti KTM / Kepegawaian</strong>
    <span>
      @if (str_starts_with($pengguna->file_bukti, 'private/'))
        <a href="{{ route('akun.bukti', $pengguna->id_pengguna) }}" target="_blank">Lihat Bukti Terunggah</a>
      @else
        {{ $pengguna->file_bukti }}
      @endif
    </span>
    <strong>Peringatan Diterima</strong><span>{{ $totalPeringatan }}</span>
  </div>
</section>
@endsection
