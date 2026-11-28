@extends('tata_letak.utama')
@section('judul', 'Kotak Masuk Admin — Campus Voice')

@section('konten')
<section class="kartu">
  <h1>Kotak Masuk Admin</h1>
  <p class="redup jarak-bawah">Pantau peringatan terkirim, pulihkan konten terhapus (&lt; 1 hari), dan kelola akun terblokir.</p>

  <div class="tab-nav">
    <a href="{{ route('admin.kotak_masuk', ['tab' => 'peringatan']) }}" class="tombol {{ $tab === 'peringatan' ? 'tombol-utama' : '' }}">Peringatan Terkirim ({{ $daftarPeringatan->count() }})</a>
    <a href="{{ route('admin.kotak_masuk', ['tab' => 'terhapus']) }}" class="tombol {{ $tab === 'terhapus' ? 'tombol-utama' : '' }}">Konten Dihapus ({{ $kontenDihapus->count() }})</a>
    <a href="{{ route('admin.kotak_masuk', ['tab' => 'terblokir']) }}" class="tombol {{ $tab === 'terblokir' ? 'tombol-utama' : '' }}">Akun Terblokir ({{ $akunTerblokir->count() }})</a>
  </div>

  @if ($tab === 'terhapus')
    @forelse ($kontenDihapus as $item)
      <div class="kartu">
        <div class="sebar">
          <strong>{{ strtoupper($item->tipe) }} &middot; {{ $item->penulis->nama }}</strong>
          <span class="teks-kecil redup">Dihapus: {{ $item->tanggal_hapus ? $item->tanggal_hapus->format('d M Y, H:i') : '-' }}</span>
        </div>
        <p class="teks-kecil jarak-atas">{{ $item->teks }}</p>
        <div class="jarak-atas">
          @if ($item->bisa_pulihkan)
            <form action="{{ route('admin.pulihkan', ['tipe' => $item->tipe, 'id' => $item->id]) }}" method="POST" style="display:inline;">
              @csrf
              <button type="submit" class="tombol tombol-sukses">Pulihkan (&lt; 1 Hari)</button>
            </form>
          @else
            <span class="teks-kecil redup">Batas waktu pemulihan (&lt; 1 hari) telah lewat.</span>
          @endif
        </div>
      </div>
    @empty
      <p class="redup">Tidak ada konten yang dihapus.</p>
    @endforelse
  @elseif ($tab === 'terblokir')
    @forelse ($akunTerblokir as $u)
      <div class="kartu sebar">
        <div>
          <strong>{{ $u->nama }}</strong> ({{ $u->nim_nip }})
          <div class="teks-kecil redup">{{ $u->peran }} &middot; Peringatan: {{ $u->peringatan_diterima_count }}</div>
        </div>
        <form action="{{ route('admin.buka_blokir', $u->id_pengguna) }}" method="POST">
          @csrf
          <button type="submit" class="tombol tombol-sukses">Buka Blokir</button>
        </form>
      </div>
    @empty
      <p class="redup">Tidak ada akun yang diblokir.</p>
    @endforelse
  @else
    @forelse ($daftarPeringatan as $w)
      <div class="kartu">
        <div class="sebar">
          <strong>Kepada: {{ $w->targetPengguna->nama }} ({{ $w->tipe_target }} #{{ $w->id_target }})</strong>
          <span class="teks-kecil redup">{{ $w->tanggal_kirim->format('d M Y, H:i') }}</span>
        </div>
        <p class="teks-kecil jarak-atas">{{ $w->pesan }}</p>
        @if ($w->siap_hapus)
          <div class="jarak-atas">
            <a href="{{ route('admin.hapus', ['tipe' => $w->tipe_target, 'id' => $w->id_target]) }}" class="tombol tombol-bahaya">Tindak Lanjut (&ge; 1 Jam)</a>
          </div>
        @endif
      </div>
    @empty
      <p class="redup">Belum ada peringatan terkirim.</p>
    @endforelse
  @endif
</section>
@endsection
