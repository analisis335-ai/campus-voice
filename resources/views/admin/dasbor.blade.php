@extends('tata_letak.utama')
@section('judul', 'Dashboard Admin — Campus Voice')

@section('konten')
<form id="form-cari" action="{{ route('admin.dasbor') }}" method="GET" class="kotak-cari {{ $kataKunci === '' ? 'tersembunyi' : '' }}">
  <input type="search" id="input-cari" name="q" value="{{ $kataKunci }}" placeholder="Cari deskripsi postingan atau nama penulis...">
  <button type="submit" class="tombol tombol-utama">Cari</button>
  <a href="{{ route('admin.dasbor') }}" class="tombol">Reset</a>
</form>

@if ($kataKunci !== '')
  <div class="pesan pesan-info">
    Menampilkan {{ $daftarPostingan->count() }} hasil untuk kata kunci "{{ $kataKunci }}".
  </div>
@endif

@forelse ($daftarPostingan as $postingan)
  @include('komponen.kartu_postingan', ['postingan' => $postingan])
@empty
  <div class="kartu">
    <p class="redup">{{ $kataKunci !== '' ? 'Postingan tidak ditemukan.' : 'Belum ada postingan aspirasi.' }}</p>
  </div>
@endforelse

<div id="modal-akun-pengguna" class="modal-latar tersembunyi" role="dialog" aria-modal="true">
  <div class="modal-isi">
    <div class="sebar jarak-bawah">
      <h2>Data Akun Pengguna</h2>
      <button type="button" id="tutup-modal-akun" class="tombol">Tutup</button>
    </div>
    <div class="daftar-baris">
      <strong>Nama</strong><span id="m-nama"></span>
      <strong>NIM / NIP</strong><span id="m-nim"></span>
      <strong>Peran</strong><span id="m-peran"></span>
      <strong>Status Akun</strong><span id="m-status"></span>
      <strong>Total Peringatan</strong><span id="m-peringatan"></span>
      <strong>Bukti Akun</strong><span id="m-bukti-wadah"></span>
    </div>
  </div>
</div>
@endsection

@push('skrip')
<script>
document.getElementById('tombol-toggle-cari')?.addEventListener('click', () => {
  const form = document.getElementById('form-cari');
  form.classList.toggle('tersembunyi');
  if (!form.classList.contains('tersembunyi')) {
    document.getElementById('input-cari').focus();
  }
});

const modal = document.getElementById('modal-akun-pengguna');
document.querySelectorAll('[data-lihat-akun]').forEach((btn) => {
  btn.addEventListener('click', () => {
    document.getElementById('m-nama').textContent = btn.dataset.nama;
    document.getElementById('m-nim').textContent = btn.dataset.nim;
    document.getElementById('m-peran').textContent = btn.dataset.peran;
    document.getElementById('m-status').textContent = btn.dataset.status;
    document.getElementById('m-peringatan').textContent = btn.dataset.peringatan + ' kali';

    const wadahBukti = document.getElementById('m-bukti-wadah');
    wadahBukti.textContent = '';
    if (btn.dataset.buktiUrl) {
      const link = document.createElement('a');
      link.href = btn.dataset.buktiUrl;
      link.target = '_blank';
      link.textContent = 'Lihat File Bukti';
      wadahBukti.appendChild(link);
    } else {
      wadahBukti.textContent = btn.dataset.buktiNama || '-';
    }

    modal.classList.remove('tersembunyi');
  });
});

document.getElementById('tutup-modal-akun')?.addEventListener('click', () => {
  modal.classList.add('tersembunyi');
});
</script>
@endpush
