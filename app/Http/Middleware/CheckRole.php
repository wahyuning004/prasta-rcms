<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Pastikan pengguna sudah login
        if (! $request->user()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        $userRole = strtolower(trim($request->user()->role ?? ''));

        // 2. Normalisasi daftar role yang diizinkan (mendukung parameter dipisah koma)
        $allowedRoles = [];
        foreach ($roles as $roleGroup) {
            foreach (explode(',', $roleGroup) as $r) {
                $roleClean = strtolower(trim($r));
                $allowedRoles[] = $roleClean;

                // Pemetaan alias peran
                if ($roleClean === 'admin') {
                    $allowedRoles[] = 'direktur';
                } elseif ($roleClean === 'klien') {
                    $allowedRoles[] = 'klient';
                    $allowedRoles[] = 'client';
                } elseif ($roleClean === 'legal') {
                    $allowedRoles[] = 'konsultan';
                } elseif ($roleClean === 'officer' || $roleClean === 'petugas') {
                    $allowedRoles[] = 'officer';
                    $allowedRoles[] = 'petugas';
                } elseif (in_array($roleClean, ['staff', 'staf'])) {
                    $allowedRoles = array_merge($allowedRoles, [
                        'marketing', 'legal', 'finance', 'officer', 'konsultan', 'petugas',
                    ]);
                }
            }
        }

        // 3. Verifikasi apakah role pengguna termasuk yang diizinkan (RBAC ketat)
        if (! in_array($userRole, $allowedRoles)) {
            $dashboardRoute = $request->user()->getDashboardRoute();

            return redirect()->route($dashboardRoute)
                ->with('error', 'Akses ditolak (RBAC). Akun Anda ('.$request->user()->getRoleLabel().') tidak memiliki izin untuk membuka halaman tersebut.');
        }

        return $next($request);
    }
}
