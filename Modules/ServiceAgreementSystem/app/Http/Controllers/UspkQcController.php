<?php

namespace Modules\ServiceAgreementSystem\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\ServiceAgreementSystem\Models\UspkBlockProgress;
use Modules\ServiceAgreementSystem\Models\UspkQcVerification;
use Modules\ServiceAgreementSystem\Models\UspkQcVerificationLog;
use Modules\ServiceAgreementSystem\Models\UspkSubmission;

class UspkQcController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isQcCoordinator = $this->isQcCoordinator($user);

        $query = UspkSubmission::query()
            ->whereNotNull('submitter_signed_spk_document_path')
            ->whereNotNull('qc_status')
            ->with([
                'department',
                'subDepartment',
                'submitter',
                'qcAssigner',
                'qcVerifications.verifier',
            ])
            ->orderByDesc('updated_at');

        if (!$isQcCoordinator) {
            $query->whereHas('qcVerifications', function ($verificationQuery) use ($user) {
                $verificationQuery->where('user_id', (int) $user->id);
            });
        }

        $submissions = $query->paginate(12);

        return view('serviceagreementsystem::uspk-qc.index', compact('submissions', 'isQcCoordinator'));
    }

    public function uploadSignedBySubmitter(Request $request, UspkSubmission $uspk)
    {
        $this->authorizeSubmitterOrAdmin($uspk);

        if ($uspk->status !== UspkSubmission::STATUS_APPROVED) {
            abort(422, 'USPK belum pada tahap final approval.');
        }

        if (!$uspk->hasFinalSpkDocument()) {
            abort(422, 'Dokumen SPK final dari Legal belum tersedia.');
        }

        $validated = $request->validate([
            'signed_spk_document' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        DB::transaction(function () use ($request, $uspk) {
            $statusBefore = (string) ($uspk->qc_status ?? '');
            $existingVerifierCount = $uspk->qcVerifications()->count();

            if ($uspk->submitter_signed_spk_document_path) {
                Storage::disk('public')->delete($uspk->submitter_signed_spk_document_path);
            }

            $path = $request->file('signed_spk_document')->store('uspk/submitter-signed-spk', 'public');

            $uspk->qcVerifications()->delete();

            $uspk->update([
                'submitter_signed_spk_document_path' => $path,
                'submitter_signed_spk_uploaded_by' => auth()->id(),
                'submitter_signed_spk_uploaded_at' => now(),
                'qc_status' => UspkSubmission::QC_STATUS_PENDING_ASSIGNMENT,
                'qc_assigned_by' => null,
                'qc_assigned_at' => null,
                'work_reported_completed_at' => null,
            ]);

            $this->appendQcLog(
                submission: $uspk,
                action: 'submitter_signed_spk_uploaded',
                statusBefore: $statusBefore,
                statusAfter: UspkSubmission::QC_STATUS_PENDING_ASSIGNMENT,
                comment: 'Pengaju mengunggah ulang SPK yang sudah ditandatangani.',
                meta: [
                    'deleted_previous_verifier_count' => $existingVerifierCount,
                ]
            );
        });

        return redirect()->route('sas.uspk.show', $uspk)->with('success', 'SPK bertanda tangan berhasil diupload. USPK otomatis masuk ke antrian QC untuk penunjukan verifier.');
    }

    public function assignVerifiers(Request $request, UspkSubmission $uspk)
    {
        $this->authorizeQcCoordinator();

        if (!$uspk->hasSubmitterSignedSpkDocument()) {
            abort(422, 'Pengaju belum mengunggah SPK bertanda tangan.');
        }

        $validated = $request->validate([
            'verifier_ids' => ['required', 'array', 'min:1'],
            'verifier_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ]);

        $verifierIds = collect($validated['verifier_ids'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $availableUserIds = User::query()
            ->whereHas('moduleRoles', function ($query) {
                $query->where('module_key', 'sas');
            })
            ->whereIn('id', $verifierIds->all())
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $invalidVerifierIds = $verifierIds->diff($availableUserIds);
        if ($invalidVerifierIds->isNotEmpty()) {
            abort(422, 'Terdapat user verifier yang tidak terdaftar pada modul SAS.');
        }

        $hasActedVerifier = $uspk->qcVerifications()
            ->where('status', '!=', \Modules\ServiceAgreementSystem\Models\UspkQcVerification::STATUS_PENDING)
            ->exists();

        if ($hasActedVerifier && !$this->isSasAdmin(auth()->user())) {
            abort(422, 'Daftar Verifier tidak dapat diubah karena sudah ada verifier yang mengambil keputusan.');
        }

        DB::transaction(function () use ($uspk, $verifierIds) {
            $statusBefore = (string) ($uspk->qc_status ?? '');
            $previousAssignments = $uspk->qcVerifications()
                ->with('verifier:id,name')
                ->get()
                ->map(fn (UspkQcVerification $verification) => [
                    'verification_id' => (int) $verification->id,
                    'user_id' => (int) $verification->user_id,
                    'verifier_name' => (string) optional($verification->verifier)->name,
                    'status' => (string) $verification->status,
                    'verified_at' => optional($verification->verified_at)?->toDateTimeString(),
                ])
                ->values()
                ->all();

            $uspk->qcVerifications()->delete();

            foreach ($verifierIds as $verifierId) {
                $uspk->qcVerifications()->create([
                    'user_id' => $verifierId,
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                    'status' => UspkQcVerification::STATUS_PENDING,
                ]);
            }

            $uspk->update([
                'qc_status' => $uspk->work_reported_completed_at
                    ? UspkSubmission::QC_STATUS_IN_VERIFICATION
                    : UspkSubmission::QC_STATUS_ASSIGNED,
                'qc_assigned_by' => auth()->id(),
                'qc_assigned_at' => now(),
            ]);

            $this->appendQcLog(
                submission: $uspk,
                action: 'verifier_assignment_saved',
                statusBefore: $statusBefore,
                statusAfter: $uspk->work_reported_completed_at
                    ? UspkSubmission::QC_STATUS_IN_VERIFICATION
                    : UspkSubmission::QC_STATUS_ASSIGNED,
                comment: 'Penugasan verifier QC disimpan.',
                meta: [
                    'previous_assignment_count' => count($previousAssignments),
                    'new_assignment_count' => $verifierIds->count(),
                    'previous_assignments' => $previousAssignments,
                    'new_verifier_ids' => $verifierIds->all(),
                ]
            );
        });

        return redirect()->route('sas.uspk.show', $uspk)->with('success', 'Verifier QC berhasil ditetapkan. Menunggu pengaju melaporkan pekerjaan selesai.');
    }

    public function reportWorkCompleted(Request $request, UspkSubmission $uspk)
    {
        $this->authorizeSubmitterOrAdmin($uspk);

        if (!$uspk->hasSubmitterSignedSpkDocument()) {
            abort(422, 'SPK bertanda tangan belum tersedia.');
        }

        $totalBlocks = (int) $uspk->blocks->count();
        $completedBlocks = $uspk->blockProgresses()
            ->where('status', UspkBlockProgress::STATUS_COMPLETED)
            ->count();

        if ($totalBlocks <= 0) {
            abort(422, 'USPK belum memiliki blok untuk direkap progresnya.');
        }

        if ($completedBlocks < $totalBlocks) {
            abort(422, 'Laporan selesai hanya bisa dikirim jika semua blok sudah status selesai.');
        }

        DB::transaction(function () use ($uspk) {
            $statusBefore = (string) ($uspk->qc_status ?? '');

            if ($uspk->qcVerifications()->exists()) {
                $uspk->qcVerifications()->update([
                    'status' => UspkQcVerification::STATUS_PENDING,
                    'comment' => null,
                    'verified_at' => null,
                ]);
            }

            $newStatus = $uspk->qcVerifications()->exists()
                ? UspkSubmission::QC_STATUS_IN_VERIFICATION
                : UspkSubmission::QC_STATUS_PENDING_ASSIGNMENT;

            $uspk->update([
                'qc_status' => $newStatus,
                'work_reported_completed_at' => now(),
            ]);

            $this->appendQcLog(
                submission: $uspk,
                action: 'work_reported_completed',
                statusBefore: $statusBefore,
                statusAfter: $newStatus,
                comment: $newStatus === UspkSubmission::QC_STATUS_IN_VERIFICATION
                    ? 'Pengaju melaporkan pekerjaan selesai dan verifikasi siap berjalan.'
                    : 'Pengaju melaporkan pekerjaan selesai. Menunggu penugasan verifier QC.',
            );
        });

        return redirect()->route('sas.uspk.show', $uspk)->with('success', 'Laporan pekerjaan selesai dikirim. Verifier QC dapat mulai melakukan verifikasi.');
    }

    public function saveBlockProgress(Request $request, UspkSubmission $uspk)
    {
        if (!$uspk->hasSubmitterSignedSpkDocument()) {
            abort(422, 'SPK bertanda tangan belum tersedia. Pengaju harus upload SPK bertanda tangan terlebih dahulu.');
        }

        $canManageBlockDeadlines = $this->isQcCoordinator(auth()->user()) || $this->isSasAdmin(auth()->user());
        $canManageBlockCompletion = ((int) $uspk->submitted_by === (int) auth()->id() || $this->isSasAdmin(auth()->user()));

        if (!$canManageBlockDeadlines && !$canManageBlockCompletion) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengurus deadline atau progres blok.');
        }

        $availableBlockIds = $uspk->blocks
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        if ($availableBlockIds->isEmpty()) {
            abort(422, 'USPK ini belum memiliki blok untuk dikelola progresnya.');
        }

        $validated = $request->validate([
            'block_ids' => ['required', 'array', 'min:1'],
            'block_ids.*' => ['integer'],
            'completed_blocks' => ['nullable', 'array'],
            'completed_blocks.*' => ['integer'],
            'deadline_at' => ['nullable', 'array'],
            'deadline_at.*' => ['nullable', 'date'],
        ]);

        $formBlockIds = collect($validated['block_ids'])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $invalidBlockIds = $formBlockIds->diff($availableBlockIds);
        if ($invalidBlockIds->isNotEmpty()) {
            abort(422, 'Terdapat blok yang tidak sesuai dengan daftar blok USPK.');
        }

        $completedBlockIds = collect($validated['completed_blocks'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        DB::transaction(function () use ($uspk, $availableBlockIds, $formBlockIds, $completedBlockIds, $validated, $canManageBlockDeadlines, $canManageBlockCompletion) {
            $statusBefore = (string) ($uspk->qc_status ?? '');
            
            $didUpdateDeadline = false;
            $didUpdateCompletion = false;

            foreach ($availableBlockIds as $blockId) {
                if (!$formBlockIds->contains($blockId)) {
                    continue;
                }

                $progress = $uspk->blockProgresses()->firstOrNew([
                    'block_id' => $blockId,
                ]);

                if ($canManageBlockDeadlines && array_key_exists('deadline_at', $validated)) {
                    $deadlineAt = data_get($validated, 'deadline_at.' . $blockId);
                    $progress->deadline_at = $deadlineAt ?: null;
                    $didUpdateDeadline = true;
                }

                if ($canManageBlockCompletion && array_key_exists('completed_blocks', $validated)) {
                    $isCompleted = $completedBlockIds->contains($blockId);
                    $wasCompleted = (string) $progress->status === UspkBlockProgress::STATUS_COMPLETED;

                    $progress->status = $isCompleted
                        ? UspkBlockProgress::STATUS_COMPLETED
                        : UspkBlockProgress::STATUS_PENDING;

                    if ($isCompleted) {
                        if (!$wasCompleted) {
                            $progress->completed_at = now();
                            $progress->completed_by = auth()->id();
                        }
                    } else {
                        $progress->completed_at = null;
                        $progress->completed_by = null;
                    }
                    $didUpdateCompletion = true;
                }

                $progress->save();
            }

            $uspk->load('blockProgresses');

            $totalBlocks = (int) $availableBlockIds->count();
            $completedBlocks = (int) $uspk->blockProgresses
                ->where('status', UspkBlockProgress::STATUS_COMPLETED)
                ->count();
            $progressPercent = $totalBlocks > 0
                ? (int) round(($completedBlocks / $totalBlocks) * 100)
                : 0;

            $actionStr = 'block_updated';
            $commentStr = "Update data blok. ";
            if($didUpdateDeadline) $commentStr .= "Deadline diperbarui. ";
            if($didUpdateCompletion) $commentStr .= "Progress: {$completedBlocks}/{$totalBlocks} blok ({$progressPercent}%).";

            $this->appendQcLog(
                submission: $uspk,
                action: $actionStr,
                statusBefore: $statusBefore,
                statusAfter: (string) ($uspk->qc_status ?? ''),
                comment: trim($commentStr),
                meta: [
                    'did_update_deadline' => $didUpdateDeadline,
                    'did_update_completion' => $didUpdateCompletion,
                    'completed_blocks' => $completedBlocks,
                    'total_blocks' => $totalBlocks,
                    'progress_percent' => $progressPercent,
                ]
            );
        });

        return redirect()->route('sas.uspk.show', $uspk)->with('success', 'Data status blok berhasil diperbarui.');
    }

    public function verifyWork(Request $request, UspkSubmission $uspk)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approved,rejected'],
            'comment' => ['nullable', 'string'],
        ]);

        if ($uspk->qc_status !== UspkSubmission::QC_STATUS_IN_VERIFICATION) {
            abort(422, 'Proses QC belum berada di tahap verifikasi.');
        }

        $isAdmin = $this->isSasAdmin(auth()->user());
        $verification = $uspk->qcVerifications()
            ->where('user_id', (int) auth()->id())
            ->first();

        if ($isAdmin) {
            $assignedVerifications = $uspk->qcVerifications()
                ->get(['id', 'user_id', 'status']);

            if ($assignedVerifications->isEmpty()) {
                abort(422, 'Belum ada verifier QC yang ditugaskan untuk USPK ini.');
            }

            $statusBeforeSummary = $assignedVerifications
                ->map(fn ($item) => (string) $item->status)
                ->unique()
                ->values()
                ->implode(',');

            $uspk->qcVerifications()->update([
                'status' => $validated['action'],
                'comment' => '[Admin Override] ' . ($validated['comment'] ?? ''),
                'verified_at' => now(),
            ]);

            $this->appendQcLog(
                submission: $uspk,
                action: 'admin_override_verifiers',
                statusBefore: $statusBeforeSummary ?: 'pending',
                statusAfter: (string) $validated['action'],
                comment: $validated['comment'] ?? null,
                meta: [
                    'affected_verification_count' => (int) $assignedVerifications->count(),
                    'affected_verification_ids' => $assignedVerifications->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                    'affected_verifier_user_ids' => $assignedVerifications->pluck('user_id')->map(fn ($id) => (int) $id)->values()->all(),
                ]
            );
        } else {
            if (!$verification) {
                abort(403, 'Anda bukan verifier yang ditugaskan untuk USPK ini.');
            }

            $verificationStatusBefore = (string) $verification->status;

            $verification->update([
                'status' => $validated['action'],
                'comment' => $validated['comment'] ?? null,
                'verified_at' => now(),
            ]);

            $this->appendQcLog(
                submission: $uspk,
                action: 'verifier_decision_recorded',
                statusBefore: $verificationStatusBefore,
                statusAfter: (string) $validated['action'],
                comment: $validated['comment'] ?? null,
                verificationId: (int) $verification->id,
                meta: [
                    'verifier_user_id' => (int) $verification->user_id,
                ]
            );
        }

        $hasRejected = $uspk->qcVerifications()->where('status', UspkQcVerification::STATUS_REJECTED)->exists();
        $hasPending = $uspk->qcVerifications()->where('status', UspkQcVerification::STATUS_PENDING)->exists();

        if ($hasRejected) {
            $this->transitionSubmissionQcStatus(
                submission: $uspk,
                newStatus: UspkSubmission::QC_STATUS_REVISION_REQUIRED,
                action: 'submission_marked_revision_required',
                comment: 'Minimal satu verifier menolak, pekerjaan perlu revisi.'
            );

            return redirect()->route('sas.uspk.show', $uspk)->with('success', 'Verifikasi tersimpan. USPK memerlukan perbaikan pekerjaan sebelum verifikasi ulang.');
        }

        if (!$hasPending) {
            $this->transitionSubmissionQcStatus(
                submission: $uspk,
                newStatus: UspkSubmission::QC_STATUS_VERIFIED,
                action: 'submission_marked_verified',
                comment: 'Semua verifier QC sudah menyetujui hasil pekerjaan.'
            );

            return redirect()->route('sas.uspk.show', $uspk)->with('success', 'Semua verifier telah approve. USPK selesai diverifikasi QC.');
        }

        return redirect()->route('sas.uspk.show', $uspk)->with('success', 'Verifikasi Anda tersimpan. Menunggu verifier lainnya.');
    }

    public function getAssignableUsers()
    {
        return User::query()
            ->whereHas('moduleRoles', function ($query) {
                $query->where('module_key', 'sas');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'position']);
    }

    protected function authorizeQcCoordinator(): void
    {
        if (!$this->isQcCoordinator(auth()->user())) {
            abort(403, 'Hanya QC Coordinator atau admin yang dapat menentukan verifier.');
        }
    }

    protected function authorizeSubmitterOrAdmin(UspkSubmission $uspk): void
    {
        $isOwner = (int) $uspk->submitted_by === (int) auth()->id();
        if ($isOwner || $this->isSasAdmin(auth()->user())) {
            return;
        }

        abort(403, 'Hanya pengaju USPK atau admin yang dapat menjalankan aksi ini.');
    }

    protected function authorizeDeadlineUpdate(UspkSubmission $uspk): void
    {
        if ($this->isQcCoordinator(auth()->user()) || $this->isSasAdmin(auth()->user())) {
            return;
        }

        abort(403, 'Hanya QC Coordinator atau admin yang dapat mengatur deadline blok.');
    }

    protected function authorizeCompletionUpdate(UspkSubmission $uspk): void
    {
        $isOwner = (int) $uspk->submitted_by === (int) auth()->id();
        if ($isOwner || $this->isSasAdmin(auth()->user())) {
            return;
        }

        abort(403, 'Hanya pengaju USPK atau admin yang dapat memperbarui status selesai blok.');
    }

    protected function isQcCoordinator($user): bool
    {
        $role = strtolower(trim((string) $user?->moduleRole('sas')));

        return in_array($role, ['qc', 'admin'], true) || $user?->hasAnyRole(['Admin', 'Super Admin']);
    }

    protected function isSasAdmin($user): bool
    {
        $role = strtolower(trim((string) $user?->moduleRole('sas')));

        return $role === 'admin' || $user?->hasAnyRole(['Admin', 'Super Admin']);
    }

    protected function transitionSubmissionQcStatus(
        UspkSubmission $submission,
        string $newStatus,
        string $action,
        ?string $comment = null,
        array $meta = []
    ): void {
        $statusBefore = (string) ($submission->qc_status ?? '');

        if ($statusBefore === $newStatus) {
            return;
        }

        $submission->update(['qc_status' => $newStatus]);

        $this->appendQcLog(
            submission: $submission,
            action: $action,
            statusBefore: $statusBefore,
            statusAfter: $newStatus,
            comment: $comment,
            meta: $meta
        );
    }

    protected function appendQcLog(
        UspkSubmission $submission,
        string $action,
        ?string $statusBefore = null,
        ?string $statusAfter = null,
        ?string $comment = null,
        ?int $verificationId = null,
        array $meta = []
    ): void {
        UspkQcVerificationLog::create([
            'uspk_submission_id' => (int) $submission->id,
            'uspk_qc_verification_id' => $verificationId,
            'actor_id' => auth()->id(),
            'action' => $action,
            'status_before' => $statusBefore,
            'status_after' => $statusAfter,
            'comment' => $comment,
            'meta' => $meta ?: null,
        ]);
    }
}
