<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekPeran
{
    public function handle(Request $request, Closure $next, string ...$peranDiizinkan)
    {
        if (!Auth::check()) {
            return redirect()->route($request->is('admin*') ? 'admin.masuk' : 'masuk');
        }

        $pengguna = Auth::user();
        if ($pengguna->status === 'Diblokir') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('masuk')
                ->with('galat', 'Akun Anda sedang diblokir oleh Admin sehingga tidak dapat mengakses sistem.');
        }

        if ($peranDiizinkan && !in_array($pengguna->peran, $peranDiizinkan, true)) {
            $tujuan = $pengguna->peran === 'Admin' ? 'admin.dasbor' : 'dasbor';
            return redirect()->route($tujuan)->with('galat', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
