<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CommonAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Kalau sudah login, lempar ke dashboard sesuai role
            $role = Auth::user()->role;
            return match ($role) {
                'admin' => redirect('/admin-dashboard'),
                'sarpras' => redirect('/sarpras-dashboard'),
                default => redirect('/dashboard'),
            };
        }
        return $next($request);
    }
}