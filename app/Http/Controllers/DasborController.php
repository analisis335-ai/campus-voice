<?php

namespace App\Http\Controllers;

use App\Models\BuktiPenyelesaian;
use App\Models\Komentar;
use App\Models\Pengguna;
use App\Models\Peringatan;
use App\Models\Postingan;
use App\Models\Suka;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DasborController extends Controller
{
    public function dasbor(Request $request)
    {
        $kataKunci = trim((string) $request->query('q', ''));

        $kueri = Postingan::with([
            'penulis.peringatanDiterima',
            'suka',
            'komentar' => fn ($q) => $q->where('status', 'Aktif')->with('penulis.peringatanDiterima')->orderBy('tanggal_komentar'),
            'buktiPenyelesaian.petugas',
        ])->where('status', 'Aktif');

        if ($kataKunci !== '') {
            $kueri->where(function ($q) use ($kataKunci) {
                $q->where('deskripsi', 'like', "%{$kataKunci}%")
                    ->orWhereHas('penulis', fn ($p) => $p->where('nama', 'like', "%{$kataKunci}%"));
            });
        }

        $daftarPostingan = $kueri->orderByDesc('tanggal_post')->get();
        $tampilan = Auth::user()->peran === 'Admin' ? 'admin.dasbor' : 'dasbor';

        return view($tampilan, compact('daftarPostingan', 'kataKunci'));
    }

    public function halamanBuatPostingan()
    {
        return view('buat_postingan');
    }

    public function simpanPostingan(Request $request)
    {
        $valid = $request->validate([
            'deskripsi' => ['required', 'string'],
            'gambar_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'deskripsi.required' => 'Deskripsi postingan tidak boleh kosong.',
            'gambar_file.image' => 'Lampiran harus berupa gambar.',
            'gambar_file.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $pathGambar = $request->hasFile('gambar_file')
            ? $request->file('gambar_file')->store('unggahan_postingan', 'public')
            : null;

        Postingan::create([
            'id_penulis' => Auth::id(),
            'deskripsi' => trim($valid['deskripsi']),
            'gambar_file' => $pathGambar,
            'tanggal_post' => now(),
            'status' => 'Aktif',
        ]);

        return redirect()->route('dasbor')->with('sukses', 'Postingan berhasil dikirim.');
    }

    public function toggleSuka(int $id)
    {
        $post = Postingan::where('id_postingan', $id)->where('status', 'Aktif')->first();
        if (!$post) {
            return back()->with('galat', 'Postingan tidak ditemukan atau telah dihapus.');
        }

        $suka = Suka::where('id_postingan', $id)->where('id_pengguna', Auth::id())->first();
        if ($suka) {
            $suka->delete();
        } else {
            Suka::create([
                'id_postingan' => $id,
                'id_pengguna' => Auth::id(),
                'tanggal_suka' => now(),
            ]);
        }

        return back();
    }

    public function simpanKomentar(Request $request, int $id)
    {
        $post = Postingan::where('id_postingan', $id)->where('status', 'Aktif')->first();
        if (!$post) {
            return back()->with('galat', 'Postingan tidak ditemukan atau telah dihapus.');
        }

        $valid = $request->validate([
            'isi' => ['required', 'string'],
        ], [
            'isi.required' => 'Komentar tidak boleh kosong.',
        ]);

        Komentar::create([
            'id_postingan' => $id,
            'id_penulis' => Auth::id(),
            'isi' => trim($valid['isi']),
            'tanggal_komentar' => now(),
            'status' => 'Aktif',
        ]);

        return back()->with('sukses', 'Komentar ditambahkan.');
    }

    public function simpanBukti(Request $request, int $id)
    {
        $post = Postingan::where('id_postingan', $id)->where('status', 'Aktif')->first();
        if (!$post) {
            return back()->with('galat', 'Postingan tidak ditemukan atau telah dihapus.');
        }

        $valid = $request->validate([
            'deskripsi' => ['required', 'string'],
            'file_bukti' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'deskripsi.required' => 'Tanggapan penyelesaian wajib diisi.',
            'file_bukti.required' => 'File atau gambar bukti wajib dilampirkan.',
            'file_bukti.mimes' => 'Format bukti harus JPG, PNG, atau PDF.',
            'file_bukti.max' => 'Ukuran file bukti maksimal 2 MB.',
        ]);

        $file = $request->file('file_bukti');
        $path = $file->store('bukti_penyelesaian', 'public');

        BuktiPenyelesaian::create([
            'id_postingan' => $id,
            'id_petugas' => Auth::id(),
            'nama_file' => $file->getClientOriginalName(),
            'path_file' => $path,
            'deskripsi' => trim($valid['deskripsi']),
            'tanggal_unggah' => now(),
        ]);

        return back()->with('sukses', 'Bukti penyelesaian berhasil dilampirkan.');
    }

    public function akun()
    {
        $pengguna = Auth::user();
        $totalPeringatan = Peringatan::hitungUntukPengguna($pengguna->id_pengguna);

        return view('akun', compact('pengguna', 'totalPeringatan'));
    }

    public function buktiAkun(int $id)
    {
        $penggunaAktif = Auth::user();
        if ($penggunaAktif->id_pengguna !== $id && $penggunaAktif->peran !== 'Admin') {
            abort(403, 'Akses ditolak.');
        }

        $target = Pengguna::findOrFail($id);
        $lokasi = storage_path('app/' . $target->file_bukti);
        if (!is_file($lokasi)) {
            abort(404, 'File bukti belum tersedia di penyimpanan.');
        }

        return response()->file($lokasi);
    }

    public function kotakMasuk(Request $request)
    {
        $tab = $request->query('tab', 'peringatan');
        $idPengguna = Auth::id();

        $daftarPeringatan = Peringatan::where('id_pengguna_target', $idPengguna)
            ->orderByDesc('tanggal_kirim')
            ->get();
        $daftarPostingan = Postingan::where('id_penulis', $idPengguna)
            ->where('status', 'Aktif')
            ->orderByDesc('tanggal_post')
            ->get();
        $daftarKomentar = Komentar::where('id_penulis', $idPengguna)
            ->where('status', 'Aktif')
            ->orderByDesc('tanggal_komentar')
            ->get();

        return view('kotak_masuk', compact('tab', 'daftarPeringatan', 'daftarPostingan', 'daftarKomentar'));
    }

    public function hapusPostinganSendiri(int $id)
    {
        $post = Postingan::where('id_postingan', $id)
            ->where('id_penulis', Auth::id())
            ->where('status', 'Aktif')
            ->firstOrFail();

        $post->update(['status' => 'Dihapus', 'tanggal_hapus' => now()]);

        return redirect()->route('kotak_masuk', ['tab' => 'postingan'])
            ->with('sukses', 'Postingan Anda berhasil dihapus.');
    }

    public function hapusKomentarSendiri(int $id)
    {
        $komen = Komentar::where('id_komentar', $id)
            ->where('id_penulis', Auth::id())
            ->where('status', 'Aktif')
            ->firstOrFail();

        $komen->update(['status' => 'Dihapus', 'tanggal_hapus' => now()]);

        return redirect()->route('kotak_masuk', ['tab' => 'komentar'])
            ->with('sukses', 'Komentar Anda berhasil dihapus.');
    }

    public function pengaturan()
    {
        return view(Auth::user()->peran === 'Admin' ? 'admin.pengaturan' : 'pengaturan');
    }

    public function ubahSandi(Request $request)
    {
        $request->validate([
            'sandi_lama' => ['required', 'string'],
            'sandi_baru' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'sandi_baru.min' => 'Password baru minimal 6 karakter.',
            'sandi_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $pengguna = Auth::user();
        if (!Hash::check($request->sandi_lama, $pengguna->kata_sandi)) {
            return back()->with('galat', 'Password saat ini tidak sesuai.');
        }

        $pengguna->update(['kata_sandi' => Hash::make($request->sandi_baru)]);

        return back()->with('sukses', 'Password berhasil diperbarui.');
    }
}
