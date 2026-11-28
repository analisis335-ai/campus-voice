@extends('tata_letak.utama')
@section('judul', 'Beri Peringatan — Campus Voice')

@section('konten')
<section class="kartu">
  <h1>Kirim Peringatan</h1>
  <p class="redup jarak-bawah">Peringatan akan dikirim ke kotak masuk pengguna dan waktu pengiriman dicatat oleh server.</p>

  <div class="pesan pesan-info">
    <strong>Target ({{ $tipe }}) milik {{ $target['penulis']->nama }} ({{ $target['penulis']->nim_nip }})</strong>
    <p class="teks-kecil jarak-atas">{{ $target['teks'] }}</p>
  </div>

  <form action="{{ route('admin.peringatan.kirim', ['tipe' => $tipe, 'id' => $id]) }}" method="POST">
    @csrf
    <div class="grup-input">
      <label for="isi-peringatan">Deskripsi Peringatan</label>
      <textarea id="isi-peringatan" name="pesan" placeholder="Jelaskan alasan peringatan dan imbauan agar pengguna menghapus konten..." required>{{ old('pesan') }}</textarea>
    </div>

    <div class="baris-fleks">
      <button type="submit" class="tombol tombol-peringatan">Kirim Peringatan</button>
      <a href="{{ route('admin.dasbor') }}" class="tombol">Kembali ke Dashboard</a>
    </div>
  </form>
</section>
@endsection
