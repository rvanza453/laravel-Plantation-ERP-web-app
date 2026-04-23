<?php

namespace Modules\SystemISPO\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IspoRoleMiddleware
{
    private const LEGACY_ROLE_MAP = [
        'ISPO Admin' => 'HR Admin',
        'ISPO Auditor' => 'HR ISPO Auditor',
    ];

    /**
     * Restrict access to users who have one of the allowed ISPO module roles.
     *
        * Usage in routes: middleware('ispo.role:HR Admin,HR ISPO Auditor')
     */
    public function handle(Request $request, Closure $next, string ...$allowedRoles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        $ispoRole = $user->moduleRole('ispo');
        $effectiveRole = self::LEGACY_ROLE_MAP[$ispoRole] ?? $ispoRole;

        if (!$ispoRole) {
            if ($request->expectsJson()) {
                abort(403, 'Anda tidak memiliki akses ke HR Modul.');
            }

            return redirect()->route('modules.index')
                ->with('error', 'Anda tidak memiliki akses ke HR Modul. Hubungi administrator untuk mendapatkan role yang sesuai.');
        }

        if (
            !empty($allowedRoles)
            && !in_array($ispoRole, $allowedRoles, true)
            && !in_array($effectiveRole, $allowedRoles, true)
        ) {
            abort(403, 'Role Anda (' . $ispoRole . ') tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
