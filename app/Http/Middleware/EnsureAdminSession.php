<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! (bool) $request->session()->get('is_admin', false)) {
            return redirect()->route('home')->with('open_admin_login', true);
        }

        return $next($request);
    }
}
