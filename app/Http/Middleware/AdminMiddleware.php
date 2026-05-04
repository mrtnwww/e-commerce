<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario no esta autenticado, se redirige al login
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        // Si el usuario esta autenticado pero no es admin, 403
        if (! auth()->user()->is_admin) {
            abort(403);
        }

        return $next($request);
    }
}
