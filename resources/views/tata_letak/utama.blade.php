<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('judul', 'Campus Voice')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('aset/css/gaya.css') }}">
</head>
<body>
  @php
    $pengguna = auth()->user();
    $adalahAdmin = $pengguna->peran === 'Admin';
    $kelasLencana = $adalahAdmin ? 'lencana-admin' : ($pengguna->peran === 'Pegawai' ? 'lencana-pegawai' : '');
    $adaCari = request()->routeIs('dasbor', 'admin.dasbor');
    $jumlahPeringatan = $adalahAdmin ? 0 : $pengguna->peringatanDiterima()->count();
  @endphp

  <header class="bilah-atas">
    <div class="bilah-atas-isi">
      <a href="{{ route($adalahAdmin ? 'admin.dasbor' : 'dasbor') }}" class="merek">
        Campus Voice
        <span class="lencana-peran {{ $kelasLencana }}">{{ $pengguna->peran }}</span>
      </a>

      <nav class="nav-ikon">
        @if ($adalahAdmin)
          @if ($adaCari)
            <button type="button" id="tombol-toggle-cari" class="tombol-ikon" title="Cari Postingan">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
              <span>Cari</span>
            </button>
          @endif
          <a href="{{ route('admin.kotak_masuk') }}" class="tombol-ikon {{ request()->routeIs('admin.kotak_masuk') ? 'aktif' : '' }}" title="Kotak Masuk">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v12H5.17L4 17.17V4z"/><path d="M4 8l8 5 8-5"/></svg>
            <span>Kotak Masuk</span>
          </a>
          <a href="{{ route('admin.pengaturan') }}" class="tombol-ikon {{ request()->routeIs('admin.pengaturan') ? 'aktif' : '' }}" title="Pengaturan">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3h.1A1.7 1.7 0 0 0 10 3.1V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8v.1a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>
            <span>Setting</span>
          </a>
        @else
          <a href="{{ route('postingan.buat') }}" class="tombol-ikon {{ request()->routeIs('postingan.buat') ? 'aktif' : '' }}" title="Buat Postingan">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M12 8v8M8 12h8"/></svg>
            <span>Buat</span>
          </a>
          @if ($adaCari)
            <button type="button" id="tombol-toggle-cari" class="tombol-ikon" title="Cari Postingan">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
              <span>Cari</span>
            </button>
          @endif
          <a href="{{ route('kotak_masuk') }}" class="tombol-ikon {{ request()->routeIs('kotak_masuk') ? 'aktif' : '' }}" title="Kotak Masuk">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v12H5.17L4 17.17V4z"/><path d="M4 8l8 5 8-5"/></svg>
            <span>Kotak Masuk{{ $jumlahPeringatan ? " ($jumlahPeringatan)" : '' }}</span>
          </a>
          <a href="{{ route('akun') }}" class="tombol-ikon {{ request()->routeIs('akun') ? 'aktif' : '' }}" title="Akun">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c1.5-4 14.5-4 16 0"/></svg>
            <span>Akun</span>
          </a>
          <a href="{{ route('pengaturan') }}" class="tombol-ikon {{ request()->routeIs('pengaturan') ? 'aktif' : '' }}" title="Pengaturan">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3h.1A1.7 1.7 0 0 0 10 3.1V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8v.1a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>
            <span>Setting</span>
          </a>
        @endif

        <form action="{{ route('keluar') }}" method="POST" style="display:inline;">
          @csrf
          <button type="submit" class="tombol-ikon" title="Keluar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
            <span>Keluar</span>
          </button>
        </form>
      </nav>
    </div>
  </header>

  <main class="wadah">
    @if (session('sukses'))
      <div class="pesan pesan-sukses">{{ session('sukses') }}</div>
    @endif
    @if (session('galat'))
      <div class="pesan pesan-bahaya">{{ session('galat') }}</div>
    @endif
    @if ($errors->any())
      <div class="pesan pesan-bahaya">{{ $errors->first() }}</div>
    @endif

    @yield('konten')
  </main>

  @stack('skrip')
</body>
</html>
