<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak. Hanya admin yang diizinkan.'], 403);
            }
            return redirect()->route('dashboard')
                             ->with('error', '🚫 Akses ditolak. Fitur ini hanya untuk Administrator.');
        }

        return $next($request);
    }
}
