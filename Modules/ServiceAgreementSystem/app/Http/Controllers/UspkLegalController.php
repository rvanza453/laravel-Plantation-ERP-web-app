<?php

namespace Modules\ServiceAgreementSystem\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\ServiceAgreementSystem\Models\UspkApproval;
use Modules\ServiceAgreementSystem\Models\UspkSubmission;

class UspkLegalController extends Controller
{
    public function index()
    {
        $this->authorizeLegal();

        $submissions = UspkSubmission::query()
            ->where('status', UspkSubmission::STATUS_APPROVED)
            ->whereNull('legal_spk_document_path')
            ->with([
                'department',
                'subDepartment',
                'submitter',
                'selectedTender.contractor',
            ])
            ->latest()
            ->paginate(15);

        return view('serviceagreementsystem::uspk-legal.index', compact('submissions'));
    }

    public function exportDraft(UspkSubmission $uspk)
    {
        $this->authorizeLegal();
        $this->ensureLegalReviewState($uspk);

        $uspk->loadMissing([
            'department.site',
            'subDepartment',
            'job',
            'submitter',
            'selectedTender.contractor',
            'tenders.contractor',
        ]);

        $filename = 'SPK-DRAFT-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $uspk->uspk_number) . '.pdf';

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('serviceagreementsystem::uspk.pdf.spk-draft', [
                'uspk' => $uspk,
            ]);

            return $pdf->download($filename);
        }

        if (class_exists(\Dompdf\Dompdf::class)) {
            $html = view('serviceagreementsystem::uspk.pdf.spk-draft', [
                'uspk' => $uspk,
            ])->render();

            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        abort(500, 'PDF engine is not installed. Please install dompdf/dompdf or barryvdh/laravel-dompdf.');
    }

    public function uploadFinal(Request $request, UspkSubmission $uspk)
    {
        $this->authorizeLegal();
        $this->ensureLegalReviewState($uspk);

        $validated = $request->validate([
            'spk_document' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'legal_spk_notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $request, $uspk) {
            if ($uspk->legal_spk_document_path) {
                Storage::disk('public')->delete($uspk->legal_spk_document_path);
            }

            $path = $request->file('spk_document')->store('uspk/legal-spk', 'public');

            $uspk->update([
                'legal_spk_document_path' => $path,
                'legal_spk_uploaded_by' => auth()->id(),
                'legal_spk_uploaded_at' => now(),
                'legal_spk_notes' => $validated['legal_spk_notes'] ?? null,
            ]);
        });

        return redirect()->route('sas.uspk.show', $uspk)->with('success', 'Dokumen SPK final dari Legal berhasil diunggah. Tahap berikutnya: pengaju mengunggah SPK bertanda tangan untuk memulai proses QC.');
    }

    public function downloadFinal(UspkSubmission $uspk)
    {
        if (!$uspk->legal_spk_document_path) {
            return back()->with('error', 'Dokumen SPK final belum diunggah oleh Legal.');
        }

        $user = auth()->user();
        $isSubmitter = (int) $uspk->submitted_by === (int) $user->id;
        $isLegal = $this->isLegalUser($user);

        if (!$isSubmitter && !$isLegal) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh dokumen ini.');
        }

        $uspk->loadMissing('selectedTender.contractor');

        $extension = strtolower(pathinfo((string) $uspk->legal_spk_document_path, PATHINFO_EXTENSION));
        if ($extension === '') {
            $extension = 'pdf';
        }

        $uspkNumberSlug = Str::slug((string) $uspk->uspk_number, '-');
        $winnerName = (string) optional(optional($uspk->selectedTender)->contractor)->name;
        $winnerSlug = $winnerName !== '' ? Str::slug($winnerName, '-') : 'kontraktor';
        $uploadedDate = optional($uspk->legal_spk_uploaded_at)->format('Ymd') ?? now()->format('Ymd');
        $downloadName = 'SPK-FINAL-' . strtoupper($uspkNumberSlug ?: 'USPK') . '-' . strtoupper($winnerSlug) . '-' . $uploadedDate . '.' . $extension;

        return Storage::disk('public')->download($uspk->legal_spk_document_path, $downloadName);
    }

    public function returnToSelection(Request $request, UspkSubmission $uspk)
    {
        $this->authorizeLegal();
        $this->ensureLegalReviewState($uspk);

        $validated = $request->validate([
            'comment' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($uspk, $validated) {
            $schema = $uspk->department?->approvalSchemas()
                ->where('is_active', true)
                ->with('steps')
                ->first();

            if (!$schema || $schema->steps->isEmpty()) {
                throw new \RuntimeException('Skema approval tidak ditemukan atau tidak memiliki tahapan.');
            }

            // Level 1-2 adalah approval site dan tetap dipertahankan.
            // Rollback legal hanya mereset voting level 3 ke atas agar hasil vote lama tidak menumpuk.
            $startLevelToReset = 3;
            $uspk->approvals()->where('level', '>=', $startLevelToReset)->delete();

            // Recreate approval rows untuk level 3 ke atas dengan status PENDING
            foreach ($schema->steps as $step) {
                if ((int) $step->level >= $startLevelToReset) {
                    $uspk->approvals()->create([
                        'schema_id' => $schema->id,
                        'level' => $step->level,
                        'status' => UspkApproval::STATUS_PENDING,
                        'user_id' => $step->user_id,
                        'comment' => '[Rollback Legal] ' . $validated['comment'],
                    ]);
                }
            }

            $uspk->tenders()->update(['is_selected' => false]);

            $uspk->update([
                'status' => UspkSubmission::STATUS_IN_REVIEW,
                'legal_spk_document_path' => null,
                'legal_spk_uploaded_by' => null,
                'legal_spk_uploaded_at' => null,
                'legal_spk_notes' => null,
            ]);
        });

        return redirect()->route('sas.uspk.show', $uspk)->with('success', 'USPK dikembalikan ke proses voting level 3. Approval site level tetap berlaku.');
    }

    protected function authorizeLegal(): void
    {
        $user = auth()->user();
        $isLegal = $this->isLegalUser($user);

        if (!$isLegal) {
            abort(403, 'Hanya role Legal yang dapat melakukan proses dokumen SPK.');
        }
    }

    protected function isLegalUser($user): bool
    {
        if (!$user) {
            return false;
        }

        $sasRole = strtolower(trim((string) $user->moduleRole('sas')));

        return in_array($sasRole, ['legal', 'admin'], true) || $user->hasAnyRole(['Legal', 'Admin', 'Super Admin']);
    }

    protected function ensureLegalReviewState(UspkSubmission $uspk): void
    {
        if ($uspk->status !== UspkSubmission::STATUS_APPROVED) {
            abort(422, 'USPK belum berstatus approved final.');
        }

        $this->restoreWinnerFromApprovalHistory($uspk);

        if (!$uspk->selectedTender()->exists()) {
            abort(422, 'Pemenang kontraktor belum ditentukan oleh approver final.');
        }
    }

    protected function restoreWinnerFromApprovalHistory(UspkSubmission $uspk): void
    {
        $winnerTenderId = $uspk->approvals()
            ->where('status', UspkApproval::STATUS_APPROVED)
            ->whereNotNull('vote_tender_id')
            ->orderByDesc('level')
            ->orderByDesc('id')
            ->value('vote_tender_id');

        if (!$winnerTenderId) {
            return;
        }

        $winnerTender = $uspk->tenders()->whereKey($winnerTenderId)->first();
        if (!$winnerTender) {
            return;
        }

        $currentSelectedId = $uspk->tenders()->where('is_selected', true)->value('id');

        if ((int) $currentSelectedId === (int) $winnerTenderId) {
            return;
        }

        DB::transaction(function () use ($uspk, $winnerTender) {
            $uspk->tenders()->update(['is_selected' => false]);
            $winnerTender->update(['is_selected' => true]);
        });

        $uspk->unsetRelation('selectedTender');
    }
}
