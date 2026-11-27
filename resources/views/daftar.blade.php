@extends('tata_letak.polos')
@section('judul', 'Daftar Akun — Campus Voice')

@section('konten')
<section class="kartu kartu-kecil">
  <h1>Daftar Akun</h1>
  <p class="redup jarak-bawah">Lengkapi identitas kampus dan unggah bukti KTM atau kepegawaian.</p>

  @if ($errors->any())
    <div class="pesan pesan-bahaya">{{ $errors->first() }}</div>
  @endif

  <form action="{{ route('daftar.proses') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="grup-input">
      <label for="daftar-nama">Nama Lengkap</label>
      <input type="text" id="daftar-nama" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Budi Santoso" required>
    </div>

    <div class="grup-input">
      <label for="daftar-peran">Status Pengguna</label>
      <select id="daftar-peran" name="peran" required>
        <option value="Mahasiswa" {{ old('peran') === 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
        <option value="Pegawai" {{ old('peran') === 'Pegawai' ? 'selected' : '' }}>Pegawai / Tendik</option>
      </select>
    </div>

    <div class="grup-input">
      <label for="daftar-nim">NIM / NIP</label>
      <input type="text" id="daftar-nim" name="nim_nip" value="{{ old('nim_nip') }}" placeholder="Masukkan NIM atau NIP" required>
    </div>

    <div class="grup-input">
      <label for="daftar-bukti" id="label-bukti">Bukti KTM / Kepegawaian (JPG/PNG/PDF, maks. 2 MB)</label>
      <input type="file" id="daftar-bukti" name="file_bukti" accept=".jpg,.jpeg,.png,.pdf" required>
    </div>

    <div class="grup-input">
      <label for="daftar-sandi">Password</label>
      <input type="password" id="daftar-sandi" name="kata_sandi" placeholder="Minimal 6 karakter" required>
    </div>

    <button type="submit" class="tombol tombol-utama tombol-penuh">Daftar</button>
  </form>

  <p class="teks-kecil redup jarak-atas" style="text-align: center;">
    Sudah punya akun? <a href="{{ route('masuk') }}">Masuk di sini</a> &middot; <a href="{{ route('awal') }}">Kembali</a>
  </p>
</section>
@endsection
