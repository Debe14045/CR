<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Akses ditolak.');
        }

        $allowed = false;
        foreach ($roles as $role) {
            if ($user->isRole($role)) {
                $allowed = true;
                break;
            }
        }

        if (! $allowed) {
            abort(403, 'Akses ditolak untuk role Anda.');
        }

        return $next($request);
    }
}
