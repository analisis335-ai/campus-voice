@php
  $penggunaAktif = auth()->user();
  $adalahAdmin = $penggunaAktif->peran === 'Admin';
  $sudahSuka = $postingan->sudahDisukaiOleh($penggunaAktif->id_pengguna);
  $kelasPeranPenulis = $postingan->penulis->peran === 'Pegawai' ? 'lencana-pegawai' : ($postingan->penulis->peran === 'Admin' ? 'lencana-admin' : '');
  $adaBuktiFisik = str_starts_with($postingan->penulis->file_bukti, 'private/');
@endphp

<article class="kartu">
  <div class="postingan-kepala">
    <div class="baris-fleks">
      @if ($adalahAdmin)
        <button type="button" class="tombol-ikon" style="padding:0;font-weight:700;color:var(--utama);"
          data-lihat-akun
          data-nama="{{ $postingan->penulis->nama }}"
          data-nim="{{ $postingan->penulis->nim_nip }}"
          data-peran="{{ $postingan->penulis->peran }}"
          data-status="{{ $postingan->penulis->status }}"
          data-peringatan="{{ $postingan->penulis->peringatanDiterima->count() }}"
          data-bukti-nama="{{ $postingan->penulis->file_bukti }}"
          data-bukti-url="{{ $adaBuktiFisik ? route('akun.bukti', $postingan->penulis->id_pengguna) : '' }}">
          {{ $postingan->penulis->nama }}
        </button>
      @else
        <span class="penulis-nama">{{ $postingan->penulis->nama }}</span>
      @endif
      <span class="lencana-peran {{ $kelasPeranPenulis }}">{{ $postingan->penulis->peran }}</span>
    </div>
    <span class="teks-kecil redup">{{ $postingan->tanggal_post->format('d M Y, H:i') }}</span>
  </div>

  <p class="postingan-isi">{{ $postingan->deskripsi }}</p>

  @if ($postingan->gambar_file)
    <img src="{{ asset('storage/' . $postingan->gambar_file) }}" class="postingan-gambar" alt="Lampiran postingan">
  @endif

  @foreach ($postingan->buktiPenyelesaian as $bukti)
    <div class="item-bukti">
      <div class="sebar">
        <strong>Bukti Penyelesaian &middot; {{ $bukti->petugas->nama }}</strong>
        <span class="teks-kecil redup">{{ $bukti->tanggal_unggah->format('d M Y, H:i') }}</span>
      </div>
      <p class="teks-kecil" style="margin:4px 0;">{{ $bukti->deskripsi }}</p>
      @if (\Illuminate\Support\Facades\Storage::disk('public')->exists($bukti->path_file))
        <a href="{{ asset('storage/' . $bukti->path_file) }}" target="_blank" class="teks-kecil">Unduh lampiran: {{ $bukti->nama_file }}</a>
      @else
        <span class="teks-kecil redup">Lampiran: {{ $bukti->nama_file }}</span>
      @endif
    </div>
  @endforeach

  <div class="aksi-postingan">
    <form action="{{ route('postingan.suka', $postingan->id_postingan) }}" method="POST" style="display:inline;">
      @csrf
      <button type="submit" class="tombol tombol-suka {{ $sudahSuka ? 'sudah-suka' : '' }}">
        Suka ({{ $postingan->suka->count() }})
      </button>
    </form>
    <span class="teks-kecil redup">{{ $postingan->komentar->count() }} Komentar</span>

    @if ($adalahAdmin)
      <a href="{{ route('admin.peringatan', ['tipe' => 'postingan', 'id' => $postingan->id_postingan]) }}" class="tombol tombol-peringatan">
        Peringatan
      </a>
      @if ($postingan->bisaDihapusPaksa())
        <a href="{{ route('admin.hapus', ['tipe' => 'postingan', 'id' => $postingan->id_postingan]) }}" class="tombol tombol-bahaya">
          Hapus
        </a>
      @endif
    @endif
  </div>

  <div class="bagian-komentar">
    <div class="daftar-komentar">
      @foreach ($postingan->komentar as $komentar)
        @php
          $buktiKomenFisik = str_starts_with($komentar->penulis->file_bukti, 'private/');
        @endphp
        <div class="item-komentar">
          <div class="sebar">
            @if ($adalahAdmin && $komentar->penulis->peran !== 'Admin')
              <button type="button" class="tombol-ikon teks-kecil" style="padding:0;font-weight:700;color:var(--utama);"
                data-lihat-akun
                data-nama="{{ $komentar->penulis->nama }}"
                data-nim="{{ $komentar->penulis->nim_nip }}"
                data-peran="{{ $komentar->penulis->peran }}"
                data-status="{{ $komentar->penulis->status }}"
                data-peringatan="{{ $komentar->penulis->peringatanDiterima->count() }}"
                data-bukti-nama="{{ $komentar->penulis->file_bukti }}"
                data-bukti-url="{{ $buktiKomenFisik ? route('akun.bukti', $komentar->penulis->id_pengguna) : '' }}">
                {{ $komentar->penulis->nama }} ({{ $komentar->penulis->peran }})
              </button>
            @else
              <strong class="teks-kecil">{{ $komentar->penulis->nama }} ({{ $komentar->penulis->peran }})</strong>
            @endif
            <span class="teks-kecil redup">{{ $komentar->tanggal_komentar->format('d M Y, H:i') }}</span>
          </div>
          <p class="teks-kecil" style="margin-top:4px;">{{ $komentar->isi }}</p>
          @if ($adalahAdmin)
            <div class="baris-fleks jarak-atas">
              <a href="{{ route('admin.peringatan', ['tipe' => 'komentar', 'id' => $komentar->id_komentar]) }}" class="teks-kecil">Peringatkan</a>
              @if ($komentar->bisaDihapusPaksa())
                <a href="{{ route('admin.hapus', ['tipe' => 'komentar', 'id' => $komentar->id_komentar]) }}" class="teks-kecil" style="color:var(--bahaya);">Hapus</a>
              @endif
            </div>
          @endif
        </div>
      @endforeach
    </div>

    <form action="{{ route('postingan.komentar', $postingan->id_postingan) }}" method="POST" class="form-komentar">
      @csrf
      <input type="text" name="isi" placeholder="Tulis komentar..." required>
      <button type="submit" class="tombol">Kirim</button>
    </form>

    @if ($penggunaAktif->peran === 'Pegawai')
      <details class="jarak-atas">
        <summary class="teks-kecil" style="cursor:pointer;font-weight:600;color:var(--sukses);">+ Tambah Bukti Penyelesaian</summary>
        <form action="{{ route('postingan.bukti', $postingan->id_postingan) }}" method="POST" enctype="multipart/form-data" class="form-bukti">
          @csrf
          <div class="grup-input">
            <label class="teks-kecil">Tanggapan Penyelesaian</label>
            <input type="text" name="deskripsi" placeholder="Jelaskan tindak lanjut yang dilakukan..." required>
          </div>
          <div class="grup-input">
            <label class="teks-kecil">File / Gambar Bukti (JPG/PNG/PDF, maks. 2 MB)</label>
            <input type="file" name="file_bukti" accept=".jpg,.jpeg,.png,.pdf" required>
          </div>
          <button type="submit" class="tombol tombol-sukses">Unggah Bukti</button>
        </form>
      </details>
    @endif
  </div>
</article>
