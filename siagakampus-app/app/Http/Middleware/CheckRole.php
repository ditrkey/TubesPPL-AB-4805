<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        // Support role:admin|superadmin or role:admin,superadmin
        $allowedRoles = [];
        foreach ($roles as $roleGroup) {
            foreach (explode('|', $roleGroup) as $role) {
                $allowedRoles[] = strtolower(trim($role));
            }
        }

        // If Spatie Laravel-Permission is available on user model
        if (method_exists($user, 'hasAnyRole')) {
            try {
                if ($user->hasAnyRole($allowedRoles)) {
                    return $next($request);
                }
            } catch (\Throwable $e) {
                // Fallback to direct attribute check
            }
        }

        // Standard role column check (e.g. 'Admin', 'Superadmin', 'Warga Tel-U')
        $userRole = strtolower($user->role ?? '');
        if (in_array($userRole, $allowedRoles, true)) {
            return $next($request);
        }

        abort(403, 'Akses ditolak: Anda tidak memiliki wewenang (Role) untuk mengakses modul pengelola ini.');
    }
}

