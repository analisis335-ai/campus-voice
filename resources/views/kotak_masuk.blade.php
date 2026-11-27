@extends('tata_letak.utama')
@section('judul', 'Kotak Masuk — Campus Voice')

@section('konten')
<section class="kartu">
  <h1>Kotak Masuk</h1>
  <p class="redup jarak-bawah">Riwayat postingan dan komentar Anda (dapat dihapus) serta pesan peringatan dari Admin.</p>

  <div class="tab-nav">
    <a href="{{ route('kotak_masuk', ['tab' => 'peringatan']) }}" class="tombol {{ $tab === 'peringatan' ? 'tombol-utama' : '' }}">Pesan Peringatan ({{ $daftarPeringatan->count() }})</a>
    <a href="{{ route('kotak_masuk', ['tab' => 'postingan']) }}" class="tombol {{ $tab === 'postingan' ? 'tombol-utama' : '' }}">Riwayat Postingan ({{ $daftarPostingan->count() }})</a>
    <a href="{{ route('kotak_masuk', ['tab' => 'komentar']) }}" class="tombol {{ $tab === 'komentar' ? 'tombol-utama' : '' }}">Riwayat Komentar ({{ $daftarKomentar->count() }})</a>
  </div>

  @if ($tab === 'postingan')
    @forelse ($daftarPostingan as $p)
      <div class="kartu">
        <div class="sebar">
          <span class="teks-kecil redup">{{ $p->tanggal_post->format('d M Y, H:i') }}</span>
          <form action="{{ route('postingan.hapus_sendiri', $p->id_postingan) }}" method="POST" onsubmit="return confirm('Hapus postingan ini?')">
            @csrf
            <button type="submit" class="tombol tombol-bahaya">Hapus</button>
          </form>
        </div>
        <p class="jarak-atas">{{ $p->deskripsi }}</p>
      </div>
    @empty
      <p class="redup">Belum ada postingan aktif milik Anda.</p>
    @endforelse
  @elseif ($tab === 'komentar')
    @forelse ($daftarKomentar as $k)
      <div class="kartu">
        <div class="sebar">
          <span class="teks-kecil redup">{{ $k->tanggal_komentar->format('d M Y, H:i') }}</span>
          <form action="{{ route('komentar.hapus_sendiri', $k->id_komentar) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?')">
            @csrf
            <button type="submit" class="tombol tombol-bahaya">Hapus</button>
          </form>
        </div>
        <p class="jarak-atas">{{ $k->isi }}</p>
      </div>
    @empty
      <p class="redup">Belum ada komentar aktif milik Anda.</p>
    @endforelse
  @else
    @forelse ($daftarPeringatan as $w)
      <div class="pesan pesan-peringatan">
        <div class="sebar">
          <strong>Peringatan ({{ $w->tipe_target }} #{{ $w->id_target }})</strong>
          <span class="teks-kecil">{{ $w->tanggal_kirim->format('d M Y, H:i') }}</span>
        </div>
        <p class="jarak-atas">{{ $w->pesan }}</p>
      </div>
    @empty
      <p class="redup">Tidak ada pesan peringatan.</p>
    @endforelse
  @endif
</section>
@endsection
