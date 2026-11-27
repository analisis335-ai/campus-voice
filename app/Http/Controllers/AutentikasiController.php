<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AutentikasiController extends Controller
{
    public function halamanAwal()
    {
        return view('awal');
    }

    public function halamanDaftar()
    {
        return view('daftar');
    }

    public function prosesDaftar(Request $request)
    {
        $valid = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'peran' => ['required', 'in:Mahasiswa,Pegawai'],
            'nim_nip' => ['required', 'string', 'max:50', 'unique:pengguna,nim_nip'],
            'file_bukti' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'kata_sandi' => ['required', 'string', 'min:6'],
        ], [
            'nim_nip.unique' => 'NIM/NIP sudah terdaftar.',
            'file_bukti.required' => 'Bukti KTM atau kepegawaian wajib diunggah.',
            'file_bukti.mimes' => 'Format bukti harus JPG, PNG, atau PDF.',
            'file_bukti.max' => 'Ukuran file bukti maksimal 2 MB.',
            'kata_sandi.min' => 'Password minimal 6 karakter.',
        ]);

        $pathBukti = $request->file('file_bukti')->store('private/bukti_akun');

        Pengguna::create([
            'nama' => trim($valid['nama']),
            'nim_nip' => trim($valid['nim_nip']),
            'peran' => $valid['peran'],
            'file_bukti' => $pathBukti,
            'kata_sandi' => Hash::make($valid['kata_sandi']),
            'status' => 'Aktif',
        ]);

        return redirect()->route('masuk')
            ->with('sukses', 'Pendaftaran berhasil. Silakan masuk menggunakan NIM/NIP dan password Anda.');
    }

    public function halamanMasuk()
    {
        return view('masuk');
    }

    public function prosesMasuk(Request $request)
    {
        $valid = $request->validate([
            'nim_nip' => ['required', 'string'],
            'kata_sandi' => ['required', 'string'],
        ]);

        $pengguna = Pengguna::where('nim_nip', trim($valid['nim_nip']))
            ->where('peran', '!=', 'Admin')
            ->first();

        if (!$pengguna || !Hash::check($valid['kata_sandi'], $pengguna->kata_sandi)) {
            return back()->withInput()->with('galat', 'NIM/NIP atau password salah.');
        }

        if ($pengguna->status === 'Diblokir') {
            return back()->withInput()->with('galat', 'Akun Anda sedang diblokir oleh Admin sehingga tidak dapat masuk.');
        }

        Auth::login($pengguna);
        $request->session()->regenerate();

        return redirect()->route('dasbor');
    }

    public function halamanAwalAdmin()
    {
        return view('admin.awal');
    }

    public function halamanMasukAdmin()
    {
        return view('admin.masuk');
    }

    public function prosesMasukAdmin(Request $request)
    {
        $valid = $request->validate([
            'nama' => ['required', 'string'],
            'kata_sandi' => ['required', 'string'],
        ]);

        $admin = Pengguna::where('peran', 'Admin')
            ->whereRaw('LOWER(nama) = ?', [mb_strtolower(trim($valid['nama']))])
            ->first();

        if (!$admin || !Hash::check($valid['kata_sandi'], $admin->kata_sandi)) {
            return back()->withInput()->with('galat', 'Nama admin atau password salah.');
        }

        Auth::login($admin);
        $request->session()->regenerate();

        return redirect()->route('admin.dasbor');
    }

    public function keluar(Request $request)
    {
        $adalahAdmin = Auth::check() && Auth::user()->peran === 'Admin';
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($adalahAdmin ? 'admin.awal' : 'awal');
    }
}
