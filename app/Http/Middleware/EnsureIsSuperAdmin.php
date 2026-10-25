<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsSuperAdmin
{
    /**
     * Handle an incoming request.
     * Hanya memperbolehkan akses jika pengguna sudah terautentikasi dan memiliki peran superadmin atau admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk sebagai Super Admin terlebih dahulu untuk melakukan pengeditan.');
        }

        $user = auth()->user();

        if (! $user->isSuperAdmin() && ! $user->isAdmin()) {
            abort(403, 'Akses terbatas: Hanya Super Admin / Admin yang memiliki hak akses untuk mengelola data.');
        }

        return $next($request);
    }
}
