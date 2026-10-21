<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Penjaga pintu halaman: dipakai sebagai role:admin atau role:admin,patient.
 * Jika peran tidak cocok, controller TIDAK PERNAH dijalankan (403).
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, "Anda tidak berhak membuka halaman ini.");
        }

        return $next($request);
    }
}
