<?php

namespace App\Http\Controllers;

use App\Models\Komentar;
use App\Models\Pengguna;
use App\Models\Peringatan;
use App\Models\Postingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    private function ambilTarget(string $tipe, int $id): ?array
    {
        if ($tipe === 'postingan') {
            $item = Postingan::with('penulis')->find($id);
            return $item ? ['item' => $item, 'teks' => $item->deskripsi, 'penulis' => $item->penulis] : null;
        }

        if ($tipe === 'komentar') {
            $item = Komentar::with('penulis')->find($id);
            return $item ? ['item' => $item, 'teks' => $item->isi, 'penulis' => $item->penulis] : null;
        }

        return null;
    }

    public function halamanPeringatan(string $tipe, int $id)
    {
        $target = $this->ambilTarget($tipe, $id);
        if (!$target || $target['item']->status !== 'Aktif') {
            return redirect()->route('admin.dasbor')->with('galat', 'Konten target tidak ditemukan atau sudah dihapus.');
        }

        return view('admin.peringatan', compact('tipe', 'id', 'target'));
    }

    public function kirimPeringatan(Request $request, string $tipe, int $id)
    {
        $target = $this->ambilTarget($tipe, $id);
        if (!$target || $target['item']->status !== 'Aktif') {
            return redirect()->route('admin.dasbor')->with('galat', 'Konten target tidak ditemukan atau sudah dihapus.');
        }

        $valid = $request->validate([
            'pesan' => ['required', 'string'],
        ], [
            'pesan.required' => 'Deskripsi peringatan wajib diisi.',
        ]);

        Peringatan::create([
            'id_admin' => Auth::id(),
            'id_pengguna_target' => $target['penulis']->id_pengguna,
            'tipe_target' => $tipe,
            'id_target' => $id,
            'pesan' => trim($valid['pesan']),
            'tanggal_kirim' => now(),
        ]);

        return redirect()->route('admin.dasbor')
            ->with('sukses', 'Peringatan berhasil dikirim ke kotak masuk pengguna.');
    }

    public function halamanHapus(string $tipe, int $id)
    {
        $target = $this->ambilTarget($tipe, $id);
        if (!$target) {
            return redirect()->route('admin.dasbor')->with('galat', 'Konten tidak ditemukan.');
        }

        $penulis = $target['penulis'];
        $jumlahPeringatan = Peringatan::hitungUntukPengguna($penulis->id_pengguna);
        $siapHapus = $target['item']->bisaDihapusPaksa();
        $bisaBlokir = $jumlahPeringatan >= 3 && $penulis->status !== 'Diblokir' && $penulis->peran !== 'Admin';

        return view('admin.hapus', compact('tipe', 'id', 'target', 'penulis', 'jumlahPeringatan', 'siapHapus', 'bisaBlokir'));
    }

    public function hapusPaksa(string $tipe, int $id)
    {
        $target = $this->ambilTarget($tipe, $id);
        if (!$target || !$target['item']->bisaDihapusPaksa()) {
            return back()->with('galat', 'Penghapusan paksa hanya dapat dilakukan setelah 1 jam peringatan dan konten masih aktif.');
        }

        $target['item']->update([
            'status' => 'Dihapus',
            'tanggal_hapus' => now(),
        ]);

        return back()->with('sukses', 'Konten berhasil dihapus secara paksa dan dicatat di Kotak Masuk Admin.');
    }

    public function blokirAkun(int $id)
    {
        $pengguna = Pengguna::findOrFail($id);
        if ($pengguna->peran === 'Admin') {
            return back()->with('galat', 'Akun Admin tidak dapat diblokir.');
        }

        if (Peringatan::hitungUntukPengguna($pengguna->id_pengguna) < 3) {
            return back()->with('galat', 'Pemblokiran akun memerlukan minimal 3 peringatan.');
        }

        $pengguna->update(['status' => 'Diblokir']);

        return back()->with('sukses', "Akun {$pengguna->nama} berhasil diblokir.");
    }

    public function kotakMasuk(Request $request)
    {
        $tab = $request->query('tab', 'peringatan');

        $daftarPeringatan = Peringatan::with('targetPengguna')
            ->orderByDesc('tanggal_kirim')
            ->get()
            ->map(function ($w) {
                $target = $this->ambilTarget($w->tipe_target, $w->id_target);
                $w->siap_hapus = $target && $target['item']->bisaDihapusPaksa();
                return $w;
            });

        $postTerhapus = Postingan::with('penulis')->where('status', 'Dihapus')->get()
            ->map(fn ($p) => (object) [
                'tipe' => 'postingan',
                'id' => $p->id_postingan,
                'penulis' => $p->penulis,
                'teks' => $p->deskripsi,
                'tanggal_hapus' => $p->tanggal_hapus,
                'bisa_pulihkan' => $p->bisaDipulihkan(),
            ]);

        $komenTerhapus = Komentar::with('penulis')->where('status', 'Dihapus')->get()
            ->map(fn ($k) => (object) [
                'tipe' => 'komentar',
                'id' => $k->id_komentar,
                'penulis' => $k->penulis,
                'teks' => $k->isi,
                'tanggal_hapus' => $k->tanggal_hapus,
                'bisa_pulihkan' => $k->bisaDipulihkan(),
            ]);

        $kontenDihapus = $postTerhapus->concat($komenTerhapus)->sortByDesc('tanggal_hapus');
        $akunTerblokir = Pengguna::withCount('peringatanDiterima')->where('status', 'Diblokir')->get();

        return view('admin.kotak_masuk', compact('tab', 'daftarPeringatan', 'kontenDihapus', 'akunTerblokir'));
    }

    public function pulihkan(string $tipe, int $id)
    {
        $target = $this->ambilTarget($tipe, $id);
        if (!$target || !$target['item']->bisaDipulihkan()) {
            return back()->with('galat', 'Konten tidak dapat dipulihkan karena batas waktu (< 1 hari) sudah lewat.');
        }

        $target['item']->update([
            'status' => 'Aktif',
            'tanggal_hapus' => null,
        ]);

        return redirect()->route('admin.kotak_masuk', ['tab' => 'terhapus'])
            ->with('sukses', 'Konten berhasil dipulihkan ke linimasa.');
    }

    public function bukaBlokir(int $id)
    {
        $pengguna = Pengguna::findOrFail($id);
        $pengguna->update(['status' => 'Aktif']);

        return redirect()->route('admin.kotak_masuk', ['tab' => 'terblokir'])
            ->with('sukses', "Blokir akun {$pengguna->nama} telah dibuka.");
    }
}
