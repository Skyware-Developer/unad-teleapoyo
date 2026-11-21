<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Autorización por rol (RF-01, RNF-01). Uso: EnsureRole::class.':admin,coordinator' */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->active) {
            Auth::logout();
            $request->session()->invalidate();

            return redirect()->route('login');
        }

        if (! $user->hasRole(...$roles)) {
            AuditLog::record('access_denied', $request->path());
            abort(403, 'No tiene permisos para esta función.');
        }

        return $next($request);
    }
}
