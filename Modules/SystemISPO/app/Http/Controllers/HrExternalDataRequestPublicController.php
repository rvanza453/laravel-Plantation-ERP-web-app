<?php

namespace Modules\SystemISPO\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\SystemISPO\App\Models\HrExternalDataRequest;
use Modules\SystemISPO\App\Models\HrExternalDataRequestAccessLog;
use Modules\SystemISPO\App\Models\HrExternalDataRequestAttachment;
use Modules\SystemISPO\App\Models\HrExternalDataRequestShareToken;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HrExternalDataRequestPublicController extends Controller
{
    public function show(Request $request, string $token)
    {
        $shareToken = $this->resolveToken($token);

        if (!$shareToken || !$shareToken->canBeUsed()) {
            return $this->forbiddenPage(403);
        }

        $ticket = HrExternalDataRequest::query()
            ->with('attachments')
            ->findOrFail($shareToken->external_data_request_id);

        $this->touchAndLog($shareToken, $request, 'view_ticket', 200);

        return response()
            ->view('systemispo::hr.external-requests.public-show', [
                'ticket' => $ticket,
                'shareToken' => $shareToken,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }

    public function preview(Request $request, string $token, HrExternalDataRequestAttachment $attachment): BinaryFileResponse
    {
        $shareToken = $this->resolveToken($token);

        if (!$shareToken || !$shareToken->canBeUsed() || $shareToken->external_data_request_id !== $attachment->external_data_request_id) {
            abort(403, 'Akses token tidak valid.');
        }

        if (!Storage::disk('local')->exists($attachment->file_path)) {
            $this->touchAndLog($shareToken, $request, 'preview_missing', 404);
            abort(404);
        }

        $this->touchAndLog($shareToken, $request, 'preview_attachment', 200);

        return response()->file(Storage::disk('local')->path($attachment->file_path), [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'X-Robots-Tag' => 'noindex, nofollow, noarchive',
            'Content-Type' => $attachment->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . addslashes($attachment->file_name) . '"',
        ]);
    }

    public function download(Request $request, string $token, HrExternalDataRequestAttachment $attachment): BinaryFileResponse
    {
        $shareToken = $this->resolveToken($token);

        if (!$shareToken || !$shareToken->canBeUsed() || $shareToken->external_data_request_id !== $attachment->external_data_request_id) {
            abort(403, 'Akses token tidak valid.');
        }

        if ($shareToken->allow_preview_only || !$shareToken->allow_download) {
            $this->touchAndLog($shareToken, $request, 'download_blocked', 403);
            abort(403, 'Download dinonaktifkan untuk tautan ini.');
        }

        if (!Storage::disk('local')->exists($attachment->file_path)) {
            $this->touchAndLog($shareToken, $request, 'download_missing', 404);
            abort(404);
        }

        $this->touchAndLog($shareToken, $request, 'download_attachment', 200);

        return response()->download(
            Storage::disk('local')->path($attachment->file_path),
            $attachment->file_name,
            [
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'X-Robots-Tag' => 'noindex, nofollow, noarchive',
            ]
        );
    }

    private function resolveToken(string $plainToken): ?HrExternalDataRequestShareToken
    {
        return HrExternalDataRequestShareToken::query()
            ->where('token_hash', HrExternalDataRequestShareToken::hashToken($plainToken))
            ->first();
    }

    private function touchAndLog(HrExternalDataRequestShareToken $shareToken, Request $request, string $action, int $statusCode): void
    {
        $shareToken->increment('view_count');
        $shareToken->update(['last_accessed_at' => now()]);

        HrExternalDataRequestAccessLog::create([
            'share_token_id' => $shareToken->id,
            'external_data_request_id' => $shareToken->external_data_request_id,
            'action' => $action,
            'status_code' => $statusCode,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'accessed_at' => now(),
        ]);
    }

    private function forbiddenPage(int $statusCode)
    {
        return response()
            ->view('systemispo::hr.external-requests.public-denied', [], $statusCode)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }
}
