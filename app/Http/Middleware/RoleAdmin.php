<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleAdmin
{
    // Tambahkan parameter ...$roles agar bisa menerima banyak role sekaligus
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $userRole = Auth::user()->role;

        // Jika role user saat ini ada di dalam daftar role yang diizinkan, silakan masuk
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Khusus Administrator: Beri akses 'Dewa' (Bisa mengakses semua route Admin Kurikulum)
        if ($userRole === 'administrator') {
            return $next($request);
        }

        abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses ke halaman ini.');
    }
}