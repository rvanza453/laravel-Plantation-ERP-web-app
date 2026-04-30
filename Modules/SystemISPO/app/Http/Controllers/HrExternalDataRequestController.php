<?php

namespace Modules\SystemISPO\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Modules\SystemISPO\Models\HrExternalDataRequest;
use Modules\SystemISPO\Models\HrExternalDataRequestAttachment;
use Modules\SystemISPO\Models\HrExternalDataRequestShareToken;

class HrExternalDataRequestController extends Controller
{
    private const READ_ROLES = [
        'HR Admin',
        'HR Data Officer',
        'HR Manager',
        'HR Viewer',
        'HR ISPO Officer',
        'HR ISPO Auditor',
        'ISPO Admin',
        'ISPO Auditor',
    ];

    private const WRITE_ROLES = [
        'HR Admin',
        'HR Data Officer',
        'HR Manager',
        'ISPO Admin',
    ];

    private const SHARE_TOKEN_ROLES = [
        'HR Admin',
        'HR Manager',
        'ISPO Admin',
    ];

    public function index(Request $request)
    {
        $this->authorizeRoles(self::READ_ROLES);

        $query = HrExternalDataRequest::query()
            ->with(['picUser:id,name', 'attachments'])
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status_proses', $request->string('status'));
        }

        if ($request->filled('q')) {
            $keyword = '%' . trim($request->string('q')) . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_referensi', 'like', $keyword)
                    ->orWhere('deskripsi_permintaan', 'like', $keyword)
                    ->orWhere('pihak_peminta', 'like', $keyword)
                    ->orWhere('kategori_data', 'like', $keyword);
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

        return view('systemispo::hr.external-requests.index', [
            'tickets' => $tickets,
            'statusOptions' => HrExternalDataRequest::STATUS_OPTIONS,
        ]);
    }

    public function create()
    {
        $this->authorizeRoles(self::WRITE_ROLES);

        return view('systemispo::hr.external-requests.form', [
            'ticket' => new HrExternalDataRequest(),
            'picUsers' => User::query()->select('id', 'name')->orderBy('name')->get(),
            'statusOptions' => HrExternalDataRequest::STATUS_OPTIONS,
            'pihakOptions' => HrExternalDataRequest::PIHAK_PEMINTA_OPTIONS,
            'kategoriOptions' => HrExternalDataRequest::KATEGORI_DATA_OPTIONS,
            'lampiranOptions' => HrExternalDataRequestAttachment::KATEGORI_LAMPIRAN_OPTIONS,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeRoles(self::WRITE_ROLES);

        $data = $this->validatePayload($request);

        DB::transaction(function () use ($data, $request): void {
            $ticket = new HrExternalDataRequest();
            $this->fillTicket($ticket, $data, $request);
            $ticket->created_by = auth()->id();
            $ticket->updated_by = auth()->id();
            $ticket->save();

            $this->storeAttachments($ticket, $request);
        });

        return redirect()->route('hr.external-requests.index')
            ->with('success', 'Tiket permintaan data eksternal berhasil dibuat.');
    }

    public function show(HrExternalDataRequest $externalRequest)
    {
        $this->authorizeRoles(self::READ_ROLES);

        $externalRequest->load([
            'picUser:id,name',
            'attachments',
            'shareTokens' => fn ($q) => $q->latest(),
        ]);

        return view('systemispo::hr.external-requests.show', [
            'ticket' => $externalRequest,
        ]);
    }

    public function edit(HrExternalDataRequest $externalRequest)
    {
        $this->authorizeRoles(self::WRITE_ROLES);

        $externalRequest->load('attachments');

        return view('systemispo::hr.external-requests.form', [
            'ticket' => $externalRequest,
            'picUsers' => User::query()->select('id', 'name')->orderBy('name')->get(),
            'statusOptions' => HrExternalDataRequest::STATUS_OPTIONS,
            'pihakOptions' => HrExternalDataRequest::PIHAK_PEMINTA_OPTIONS,
            'kategoriOptions' => HrExternalDataRequest::KATEGORI_DATA_OPTIONS,
            'lampiranOptions' => HrExternalDataRequestAttachment::KATEGORI_LAMPIRAN_OPTIONS,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, HrExternalDataRequest $externalRequest)
    {
        $this->authorizeRoles(self::WRITE_ROLES);

        $data = $this->validatePayload($request);

        DB::transaction(function () use ($externalRequest, $data, $request): void {
            $this->fillTicket($externalRequest, $data, $request);
            $externalRequest->updated_by = auth()->id();
            $externalRequest->save();

            $this->deleteMarkedAttachments($externalRequest, $request);
            $this->storeAttachments($externalRequest, $request);
        });

        return redirect()->route('hr.external-requests.show', $externalRequest)
            ->with('success', 'Tiket berhasil diperbarui.');
    }

    public function destroyAttachment(HrExternalDataRequest $externalRequest, HrExternalDataRequestAttachment $attachment)
    {
        $this->authorizeRoles(self::WRITE_ROLES);

        abort_unless((int) $attachment->external_data_request_id === (int) $externalRequest->id, 404);

        $disk = $this->resolveAttachmentDisk($attachment->file_path);
        Storage::disk($disk)->delete($attachment->file_path);
        $attachment->delete();

        return redirect()->route('hr.external-requests.edit', $externalRequest)
            ->with('success', 'Lampiran berhasil dihapus.');
    }

    public function previewAttachment(HrExternalDataRequest $externalRequest, HrExternalDataRequestAttachment $attachment)
    {
        $this->authorizeRoles(self::READ_ROLES);
        abort_unless((int) $attachment->external_data_request_id === (int) $externalRequest->id, 404);

        $disk = $this->resolveAttachmentDisk($attachment->file_path);

        if (!Storage::disk($disk)->exists($attachment->file_path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        return Storage::disk($disk)->response($attachment->file_path, $attachment->file_name, [
            'Content-Type' => $attachment->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . addslashes($attachment->file_name) . '"',
        ], 'inline');
    }

    public function downloadAttachment(HrExternalDataRequest $externalRequest, HrExternalDataRequestAttachment $attachment)
    {
        $this->authorizeRoles(self::READ_ROLES);
        abort_unless((int) $attachment->external_data_request_id === (int) $externalRequest->id, 404);

        $disk = $this->resolveAttachmentDisk($attachment->file_path);

        if (!Storage::disk($disk)->exists($attachment->file_path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        return Storage::disk($disk)->download(
            $attachment->file_path,
            $attachment->file_name
        );
    }

    public function generateShareToken(Request $request, HrExternalDataRequest $externalRequest)
    {
        $this->authorizeRoles(self::SHARE_TOKEN_ROLES);

        $validated = $request->validate([
            'expires_at' => ['nullable', 'date', 'after:now'],
            'allow_download' => ['nullable', 'boolean'],
            'allow_preview_only' => ['nullable', 'boolean'],
            'max_views' => ['nullable', 'integer', 'min:1', 'max:100000'],
        ]);

        $allowPreviewOnly = (bool) ($validated['allow_preview_only'] ?? false);
        $allowDownload = $allowPreviewOnly ? false : (bool) ($validated['allow_download'] ?? true);

        $plainToken = HrExternalDataRequestShareToken::generatePlainToken();

        HrExternalDataRequestShareToken::create([
            'external_data_request_id' => $externalRequest->id,
            'token_hash' => HrExternalDataRequestShareToken::hashToken($plainToken),
            'token_hint' => substr($plainToken, 0, 12),
            'allow_download' => $allowDownload,
            'allow_preview_only' => $allowPreviewOnly,
            'max_views' => $validated['max_views'] ?? null,
            'expires_at' => isset($validated['expires_at'])
                ? Carbon::parse($validated['expires_at'])
                : now()->addDays(7),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('hr.external-requests.show', $externalRequest)
            ->with('success', 'Token share berhasil dibuat.')
            ->with('share_link', route('hr.external-requests.public.show', ['token' => $plainToken]));
    }

    public function revokeShareToken(HrExternalDataRequest $externalRequest, HrExternalDataRequestShareToken $token)
    {
        $this->authorizeRoles(self::SHARE_TOKEN_ROLES);

        abort_unless((int) $token->external_data_request_id === (int) $externalRequest->id, 404);

        $token->update(['revoked_at' => now()]);

        return redirect()->route('hr.external-requests.show', $externalRequest)
            ->with('success', 'Token share berhasil di-revoke.');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'judul_permintaan' => ['nullable', 'string'],
            'tanggal_surat_masuk' => ['nullable', 'date'],
            'pihak_peminta' => ['nullable', 'string'],
            'kategori_data' => ['nullable', 'string'],
            'deskripsi_permintaan' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
            'pic_user_id' => ['nullable', 'exists:users,id'],
            'status_proses' => ['nullable', Rule::in(HrExternalDataRequest::STATUS_OPTIONS)],
            'catatan_internal' => ['nullable', 'string'],
            'delete_attachments' => ['nullable', 'array'],
            'delete_attachments.*' => ['nullable', 'integer'],
            'new_attachments' => ['nullable', 'array'],
            'new_attachments.*' => ['nullable', 'file', 'max:15360'],
            'new_attachment_categories' => ['nullable', 'array'],
            'new_attachment_categories.*' => ['nullable', Rule::in(HrExternalDataRequestAttachment::KATEGORI_LAMPIRAN_OPTIONS)],
        ]);
    }

    private function fillTicket(HrExternalDataRequest $ticket, array $data, Request $request): void
    {
        $statusProses = $data['status_proses'] ?? 'menunggu';

        $ticket->judul_permintaan = $data['judul_permintaan'] ?? null;
        $ticket->tanggal_surat_masuk = $data['tanggal_surat_masuk'] ?? null;
        $ticket->pihak_peminta = $data['pihak_peminta'] ?? null;
        $ticket->kategori_data = $data['kategori_data'] ?? null;
        $ticket->deskripsi_permintaan = $data['deskripsi_permintaan'] ?? null;
        $ticket->deadline = $data['deadline'] ?? null;
        $ticket->pic_user_id = $data['pic_user_id'] ?? null;
        $ticket->status_proses = $statusProses;
        $ticket->catatan_internal = $data['catatan_internal'] ?? null;

        if ($statusProses === 'selesai') {
            $ticket->tanggal_selesai = $ticket->tanggal_selesai ?: now()->toDateString();
        } elseif ($request->boolean('clear_tanggal_selesai')) {
            $ticket->tanggal_selesai = null;
        }
    }

    private function storeAttachments(HrExternalDataRequest $ticket, Request $request): void
    {
        $files = $request->file('new_attachments', []);
        $categories = $request->input('new_attachment_categories', []);

        foreach ($files as $index => $file) {
            if (!$file) {
                continue;
            }

            $category = $categories[$index] ?? HrExternalDataRequestAttachment::KATEGORI_LAMPIRAN_OPTIONS[0];
            if (!in_array($category, HrExternalDataRequestAttachment::KATEGORI_LAMPIRAN_OPTIONS, true)) {
                $category = HrExternalDataRequestAttachment::KATEGORI_LAMPIRAN_OPTIONS[0];
            }

            $storedPath = $file->store('hr-external-requests/' . $ticket->id, 'public');

            if (!$storedPath || !Storage::disk('public')->exists($storedPath)) {
                throw new \RuntimeException('Gagal menyimpan lampiran ke storage.');
            }

            HrExternalDataRequestAttachment::create([
                'external_data_request_id' => $ticket->id,
                'kategori_lampiran' => $category,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $storedPath,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now(),
            ]);
        }
    }

    private function deleteMarkedAttachments(HrExternalDataRequest $ticket, Request $request): void
    {
        $attachmentIds = collect($request->input('delete_attachments', []))
            ->filter()
            ->map(static fn ($value) => (int) $value)
            ->values();

        if ($attachmentIds->isEmpty()) {
            return;
        }

        $attachments = HrExternalDataRequestAttachment::query()
            ->where('external_data_request_id', $ticket->id)
            ->whereIn('id', $attachmentIds)
            ->get();

        foreach ($attachments as $attachment) {
            $disk = $this->resolveAttachmentDisk($attachment->file_path);
            Storage::disk($disk)->delete($attachment->file_path);
            $attachment->delete();
        }
    }

    private function resolveAttachmentDisk(string $path): string
    {
        return Storage::disk('public')->exists($path) ? 'public' : 'local';
    }

    private function authorizeRoles(array $roles): void
    {
        abort_unless(auth()->user()?->hasModuleRole('ispo', $roles), 403, 'Role Anda tidak memiliki izin untuk aksi ini.');
    }
}
