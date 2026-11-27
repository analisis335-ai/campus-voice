@extends('tata_letak.utama')
@section('judul', 'Dashboard — Campus Voice')

@section('konten')
<form id="form-cari" action="{{ route('dasbor') }}" method="GET" class="kotak-cari {{ $kataKunci === '' ? 'tersembunyi' : '' }}">
  <input type="search" id="input-cari" name="q" value="{{ $kataKunci }}" placeholder="Cari deskripsi postingan atau nama penulis...">
  <button type="submit" class="tombol tombol-utama">Cari</button>
  <a href="{{ route('dasbor') }}" class="tombol">Reset</a>
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
</script>
@endpush
