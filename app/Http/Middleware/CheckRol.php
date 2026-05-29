<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRol
{
    /**
     * Verifica que el usuario tenga uno de los roles permitidos.
     * Uso en rutas: middleware('rol:admin') o middleware('rol:admin,empleado')
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!Auth::check() || !in_array(Auth::user()->rol, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta seccion.');
        }

        return $next($request);
    }
}