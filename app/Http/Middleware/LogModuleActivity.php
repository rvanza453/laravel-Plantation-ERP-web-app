<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\PrSystem\Helpers\ActivityLogger;
use Throwable;

class LogModuleActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!Auth::check()) {
            return $response;
        }

        if ($request->routeIs('activity-logs.*') || $request->routeIs('admin.activity-logs.*')) {
            return $response;
        }

        $route = $request->route();

        if (!$route) {
            return $response;
        }

        $routeName = $route->getName();
        $method = strtoupper($request->method());
        $statusCode = (int) $response->getStatusCode();
        $system = ActivityLogger::detectSystem($routeName);

        $action = $this->resolveActionLabel($routeName, $method, $system);
        $description = $this->buildDescription($request, $routeName, $method, $statusCode);

        try {
            ActivityLogger::log($action, $description, null, [
                'system' => $system,
                'route_name' => $routeName,
                'http_method' => $method,
                'url' => $request->fullUrl(),
            ]);
        } catch (Throwable) {
            // Never block user flow when activity logging fails.
        }

        return $response;
    }

    private function resolveActionLabel(?string $routeName, string $method, string $system): string
    {
        $routeActionMap = [
            'sas.uspk.submit' => 'SAS Submit USPK',
            'sas.uspk.approve' => 'SAS Approve USPK',
            'sas.uspk.hold' => 'SAS Hold USPK',
            'sas.uspk.reject' => 'SAS Reject USPK',
            'sas.uspk.rollback-approval' => 'SAS Rollback Approval',
            'sas.uspk-legal.upload' => 'SAS Upload SPK Final',
            'sas.uspk-legal.return' => 'SAS Return to Selection',
            'sas.uspk-qc.upload-signed' => 'SAS Upload SPK TTD',
            'sas.uspk-qc.assign-verifiers' => 'SAS Assign Verifier QC',
            'sas.uspk-qc.block-progress' => 'SAS Update Progress Blok',
            'sas.uspk-qc.report-completed' => 'SAS Report Work Completed',
            'sas.uspk-qc.verify' => 'SAS Verify Work',
        ];

        if ($routeName && isset($routeActionMap[$routeName])) {
            return $routeActionMap[$routeName];
        }

        $generic = match ($method) {
            'GET' => 'Accessed',
            'POST' => 'Submitted',
            'PUT', 'PATCH' => 'Updated',
            'DELETE' => 'Deleted',
            default => 'Request',
        };

        return trim($system . ' ' . $generic);
    }

    private function buildDescription(Request $request, ?string $routeName, string $method, int $statusCode): string
    {
        $subject = $this->resolveSubjectLabel($request);

        if ($routeName === 'sas.uspk.rollback-approval') {
            return sprintf(
                'Rollback approval dijalankan%s [%s] status=%d',
                $subject ? ' untuk ' . $subject : '',
                $method,
                $statusCode
            );
        }

        return sprintf(
            '%s%s [%s] route=%s status=%d',
            $method,
            $subject ? ' ' . $subject : '',
            $method,
            $routeName ?: $request->path(),
            $statusCode
        );
    }

    private function resolveSubjectLabel(Request $request): ?string
    {
        $uspk = $request->route('uspk');
        if (is_object($uspk)) {
            $uspkNumber = (string) ($uspk->uspk_number ?? '');
            if ($uspkNumber !== '') {
                return 'USPK ' . $uspkNumber;
            }

            if (isset($uspk->id)) {
                return 'USPK#' . $uspk->id;
            }
        }

        $pr = $request->route('pr');
        if (is_object($pr)) {
            $prNumber = (string) ($pr->pr_number ?? '');
            if ($prNumber !== '') {
                return 'PR ' . $prNumber;
            }

            if (isset($pr->id)) {
                return 'PR#' . $pr->id;
            }
        }

        return null;
    }
}
