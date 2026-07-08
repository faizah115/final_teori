<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleUser
{
    /**
     * Pastikan hanya USER (bukan ADMIN) yang bisa mengakses route ini.
     * Jika ADMIN mencoba masuk → redirect ke admin dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('warning', 'Anda login sebagai Admin. Halaman ini hanya untuk user biasa.');
        }

        return $next($request);
    }
}
