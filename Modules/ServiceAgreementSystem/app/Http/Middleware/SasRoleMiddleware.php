<?php

namespace Modules\ServiceAgreementSystem\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SasRoleMiddleware
{
    /**
     * Restrict access to users who have one of the allowed SAS module roles.
     *
     * Usage in routes: middleware('sas.role:Admin,Approver')
     */
    public function handle(Request $request, Closure $next, string ...$allowedRoles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        if ($user->hasRole('Super Admin')) {
            return $next($request);
        }

        $sasRole = $this->normalizeRoleName($user->moduleRole('sas'));

        if (!$sasRole) {
            if ($request->expectsJson()) {
                abort(403, 'Anda tidak memiliki akses ke modul Service Agreement System.');
            }

            return redirect()->route('modules.index')
                ->with('error', 'Anda tidak memiliki akses ke modul Service Agreement System. Hubungi administrator untuk mendapatkan role yang sesuai.');
        }

        if (!empty($allowedRoles) && !$this->hasAllowedRole($user, $sasRole, $allowedRoles)) {
            abort(403, 'Role Anda (' . ($user->moduleRole('sas') ?? '-') . ') tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }

    protected function hasAllowedRole($user, string $sasRole, array $allowedRoles): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        $allowed = array_map(fn ($role) => $this->normalizeRoleName((string) $role), $allowedRoles);

        return in_array($sasRole, $allowed, true);
    }

    protected function normalizeRoleName(?string $role): string
    {
        $normalized = strtolower(trim((string) $role));

        return match ($normalized) {
            'asisten afdeling', 'pengaju' => 'staff',
            'manager', 'ktu', 'gm' => 'approver',
            default => $normalized,
        };
    }
}
