<x-serviceagreementsystem::layouts.master :title="'Detail USPK'">
    @php
        $sasRole = strtolower(trim((string) auth()->user()?->moduleRole('sas')));
        $isSasAdmin = $sasRole === 'admin' || auth()->user()?->hasAnyRole(['Admin', 'Super Admin']);
        $isLegalRole = in_array($sasRole, ['legal', 'admin'], true) || auth()->user()?->hasAnyRole(['Legal', 'Admin', 'Super Admin']);
        $isQcCoordinator = in_array($sasRole, ['qc', 'admin'], true) || auth()->user()?->hasAnyRole(['Admin', 'Super Admin']);
        $isSubmitter = (int) ($uspk->submitted_by ?? 0) === (int) auth()->id();
        $canDownloadFinalSpk = $uspk->hasFinalSpkDocument() && ($isSubmitter || $isLegalRole || $isSasAdmin);
        $canProcessLegal = $isLegalRole && $uspk->status === \Modules\ServiceAgreementSystem\Models\UspkSubmission::STATUS_APPROVED && !$uspk->hasFinalSpkDocument();
        $canUploadSignedSpk = ($isSubmitter || $isSasAdmin)
            && $uspk->status === \Modules\ServiceAgreementSystem\Models\UspkSubmission::STATUS_APPROVED
            && $uspk->hasFinalSpkDocument();

        $qcStatus = (string) ($uspk->qc_status ?? '');
        $qcStatusLabels = [
            \Modules\ServiceAgreementSystem\Models\UspkSubmission::QC_STATUS_PENDING_ASSIGNMENT => 'Menunggu Penugasan Verifier',
            \Modules\ServiceAgreementSystem\Models\UspkSubmission::QC_STATUS_ASSIGNED => 'Menunggu Laporan Pekerjaan Selesai',
            \Modules\ServiceAgreementSystem\Models\UspkSubmission::QC_STATUS_IN_VERIFICATION => 'Verifikasi QC Berjalan',
            \Modules\ServiceAgreementSystem\Models\UspkSubmission::QC_STATUS_VERIFIED => 'Terverifikasi QC',
            \Modules\ServiceAgreementSystem\Models\UspkSubmission::QC_STATUS_REVISION_REQUIRED => 'Perlu Revisi Pekerjaan',
        ];
        $qcHistoryActionLabels = [
            'submitter_signed_spk_uploaded' => 'Upload SPK Dengan TTD',
            'verifier_assigned' => 'Assign Verifier',
            'verifier_assignment_saved' => 'Simpan Penugasan Verifier',
            'work_reported_completed' => 'Lapor Pekerjaan Selesai',
            'block_deadline_updated' => 'Update Deadline Blok',
            'block_completion_updated' => 'Update Status Selesai Blok',
            'verification_cycle_reset' => 'Reset Siklus Verifikasi',
            'verifier_decision_recorded' => 'Keputusan Verifier',
            'submission_marked_revision_required' => 'Status Jadi Butuh Revisi',
            'submission_marked_verified' => 'Status Jadi Terverifikasi',
        ];
        $importantQcHistoryLogs = $uspk->qcVerificationLogs
            ->filter(function ($log) {
                // Sembunyikan log granular lama yang terlalu detail/noise.
                if ((string) $log->action === 'verifier_assigned') {
                    return false;
                }

                if ((string) $log->action === 'verification_cycle_reset' && (string) $log->status_before === (string) $log->status_after) {
                    return false;
                }

                return true;
            })
            ->values();

        $qcAssignmentBadgeClass = $uspk->qcVerifications->isNotEmpty() ? 'badge-approved' : 'badge-pending_assignment';
        $qcAssignmentBadgeText = $uspk->qcVerifications->isNotEmpty()
            ? 'Verifier Sudah Ditugaskan'
            : 'Menunggu Penugasan Verifier';
        $qcReportBadgeClass = $uspk->work_reported_completed_at ? 'badge-approved' : 'badge-pending_assignment';
        $qcReportBadgeText = $uspk->work_reported_completed_at
            ? 'Pekerjaan Sudah Dilaporkan Selesai'
            : 'Menunggu Laporan Pekerjaan Selesai';

        $currentUserQcVerification = $uspk->qcVerifications->firstWhere('user_id', auth()->id());
        $hasSubmitterSignedSpk = $uspk->hasSubmitterSignedSpkDocument();
        $canAssignQcVerifiers = ($isQcCoordinator || $isSasAdmin) && $hasSubmitterSignedSpk;
        
        $hasActedVerifier = $uspk->qcVerifications->contains(fn($v) => (string) $v->status !== \Modules\ServiceAgreementSystem\Models\UspkQcVerification::STATUS_PENDING);
        $disableVerifierEdit = $hasActedVerifier && !$isSasAdmin; // Only admins can bypass this restriction if needed computationally, but controller blocks it generally.
        
        $canVerifyQc = ($currentUserQcVerification || $isSasAdmin)
            && $qcStatus === \Modules\ServiceAgreementSystem\Models\UspkSubmission::QC_STATUS_IN_VERIFICATION;

        $uspkBlocks = $uspk->blocks;
        $blockProgressById = $uspk->blockProgresses->keyBy(fn ($progress) => (int) $progress->block_id);
        $totalBlocks = $uspkBlocks->count();
        $completedBlocks = $uspkBlocks->filter(function ($block) use ($blockProgressById) {
            return optional($blockProgressById->get((int) $block->id))->status
                === \Modules\ServiceAgreementSystem\Models\UspkBlockProgress::STATUS_COMPLETED;
        })->count();
        $blockProgressPercent = $totalBlocks > 0 ? (int) round(($completedBlocks / $totalBlocks) * 100) : 0;
        $today = now()->startOfDay();
        $overdueBlocks = $uspkBlocks->filter(function ($block) use ($blockProgressById, $today) {
            $progress = $blockProgressById->get((int) $block->id);
            if (!$progress || !$progress->deadline_at) {
                return false;
            }

            $isCompleted = (string) $progress->status === \Modules\ServiceAgreementSystem\Models\UspkBlockProgress::STATUS_COMPLETED;

            return !$isCompleted && $progress->deadline_at->lt($today);
        })->count();
        $dueSoonBlocks = $uspkBlocks->filter(function ($block) use ($blockProgressById, $today) {
            $progress = $blockProgressById->get((int) $block->id);
            if (!$progress || !$progress->deadline_at) {
                return false;
            }

            $isCompleted = (string) $progress->status === \Modules\ServiceAgreementSystem\Models\UspkBlockProgress::STATUS_COMPLETED;

            return !$isCompleted
                && $progress->deadline_at->gte($today)
                && $progress->deadline_at->lte($today->copy()->addDays(3));
        })->count();
        $canManageBlockDeadlines = $isQcCoordinator || $isSasAdmin;
        $canManageBlockCompletion = ($isSubmitter || $isSasAdmin)
            && $uspk->hasSubmitterSignedSpkDocument()
            && $totalBlocks > 0;
        $canReportWorkCompleted = ($isSubmitter || $isSasAdmin)
            && $uspk->hasSubmitterSignedSpkDocument()
            && !$uspk->work_reported_completed_at
            && $totalBlocks > 0
            && $completedBlocks === $totalBlocks;

        $assignableQcUsers = $canAssignQcVerifiers
            ? \App\Models\User::query()
                ->whereHas('moduleRoles', function ($query) {
                    $query->where('module_key', 'sas');
                })
                ->orderBy('name')
                ->get(['id', 'name', 'position'])
            : collect();

        $approvals = $uspk->approvals;
        $hasApprovals = $approvals->count() > 0;
        $maxApprovalLevel = (int) $approvals->max('level');
        $finalLevelApproval = $approvals->firstWhere('level', $maxApprovalLevel);
        $hasPendingOrHold = $approvals->contains(fn ($approval) => in_array($approval->status, ['pending', 'on_hold'], true));
        $allApproversFinishedVoting = $hasApprovals && !$hasPendingOrHold;
        $isFinalLevelFinalized = $finalLevelApproval && in_array($finalLevelApproval->status, ['approved', 'rejected'], true);
        $isVotingFinalized = $uspk->status === \Modules\ServiceAgreementSystem\Models\UspkSubmission::STATUS_APPROVED || $allApproversFinishedVoting || $isFinalLevelFinalized;

        $winnerFromFinalVote = $approvals
            ->where('status', 'approved')
            ->whereNotNull('vote_tender_id')
            ->sortByDesc('level')
            ->first();
        $winnerTender = $winnerFromFinalVote?->voteTender ?: $uspk->tenders->firstWhere('is_selected', true);
        $winnerTenderId = (int) ($winnerTender->id ?? 0);
        
        $highestActiveApproval = $approvals->filter(fn($a) => in_array($a->status, ['approved', 'rejected', 'on_hold']))->sortByDesc('level')->first();
        $canRollbackApproval = false;
        if ($highestActiveApproval) {
            $isOwner = (int) $highestActiveApproval->user_id === (int) auth()->id();
            if ($isSasAdmin || $isOwner) {
                // Determine if owner is currently blocked because a higher level has acted
                $higherLevelActed = $approvals->contains(fn($a) => $a->level > $highestActiveApproval->level && $a->status !== 'pending');
                if (!$higherLevelActed || $isSasAdmin) {
                    $canRollbackApproval = true;
                }
            }
        }
    @endphp

    @push('actions')
        <div style="display: flex; gap: 8px;">
            @if($uspk->isEditable())
                <a href="{{ route('sas.uspk.edit', $uspk) }}" class="btn btn-outline-primary btn-sm action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('sas.uspk.submit', $uspk) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin mensubmit USPK ini?')">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm action-btn shadow-sm">
                        <i class="fas fa-paper-plane"></i> Submit USPK
                    </button>
                </form>
            @endif
            @if($canProcessLegal)
                <a href="{{ route('sas.uspk-legal.export', $uspk) }}" class="btn btn-primary btn-sm action-btn">
                    <i class="fas fa-file-export"></i> Export Draft SPK
                </a>
            @endif
            @if($canDownloadFinalSpk)
                <a href="{{ route('sas.uspk-legal.download', $uspk) }}" class="btn btn-success btn-sm action-btn">
                    <i class="fas fa-file-download"></i> Download SPK Final
                </a>
            @endif
            <a href="{{ route('sas.uspk.index') }}" class="btn btn-secondary btn-sm action-btn">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    @endpush

    {{-- Header Info --}}
    <div class="card mb-4 modern-card">
        <div class="card-header d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(0,0,0,0.05); padding: 20px 24px;">
            <div>
                <div class="card-title text-primary" style="font-size: 20px; font-weight: 800; letter-spacing: -0.5px;">{{ $uspk->uspk_number }}</div>
                <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-user-circle"></i> {{ $uspk->submitter->name ?? '-' }}
                    <span style="opacity: 0.5;">•</span>
                    <i class="far fa-clock"></i> {{ $uspk->created_at->format('d M Y H:i') }}
                </div>
            </div>
            <span class="badge badge-{{ $uspk->status }} status-badge">
                {{ ucfirst(str_replace('_', ' ', $uspk->status)) }}
            </span>
        </div>
        <div class="card-body" style="padding: 24px;">
            <div style="font-size: 22px; font-weight: 700; margin-bottom: 10px; color: var(--text-primary);">{{ $uspk->title }}</div>

            @if($uspk->description)
                <div class="desc-box">
                    {{ $uspk->description }}
                </div>
            @endif

            <div class="info-grid mt-4">
                <div class="info-item">
                    <div class="info-label">Site / Department</div>
                    <div class="info-value">{{ $uspk->department->name ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Afdeling</div>
                    <div class="info-value">{{ $uspk->subDepartment->name ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Blok Area</div>
                    <div class="info-value">
                        @if($uspk->block_ids && count($uspk->block_ids) > 0)
                            @php
                                $blockNames = \Modules\ServiceAgreementSystem\Models\Block::whereIn('id', $uspk->block_ids)->pluck('name');
                            @endphp
                            <div class="tags-container">
                                @foreach($blockNames as $blockName)
                                    <span class="tag-badge">{{ $blockName }}</span>
                                @endforeach
                            </div>
                        @elseif($uspk->block)
                            <span class="tag-badge">{{ $uspk->block->name }}</span>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Aktivitas</div>
                    <div class="info-value">{{ $uspk->job->name ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 modern-card decision-summary-card {{ $isVotingFinalized ? 'decision-summary-card--final' : 'decision-summary-card--progress' }}">
        <div class="card-body" style="padding: 18px 24px;">
            @if($isVotingFinalized && $uspk->status !== \Modules\ServiceAgreementSystem\Models\UspkSubmission::STATUS_REJECTED)
                <div class="decision-summary-title">
                    <i class="fas fa-trophy"></i>
                    Keputusan Final Sudah Ditetapkan
                </div>
                @if($winnerTender)
                    <div class="decision-summary-grid mt-2">
                        <div>
                            <div class="decision-label">Kontraktor Pemenang</div>
                            <div class="decision-value">{{ $winnerTender->contractor->name ?? '-' }}</div>
                            <div class="decision-subvalue">{{ $winnerTender->contractor->company_name ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="decision-label">Nilai Nego Final</div>
                            <div class="decision-value">Rp {{ number_format((float) $winnerTender->tender_value, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <div class="decision-label">Durasi Final</div>
                            <div class="decision-value">{{ $winnerTender->tender_duration ? $winnerTender->tender_duration . ' hari' : '-' }}</div>
                        </div>
                    </div>
                @else
                    <div class="decision-summary-note mt-2">Voting sudah selesai, tetapi pemenang belum terdeteksi otomatis. Mohon cek riwayat approval.</div>
                @endif
            @elseif($uspk->status === \Modules\ServiceAgreementSystem\Models\UspkSubmission::STATUS_REJECTED)
                <div class="decision-summary-title">
                    <i class="fas fa-times-circle"></i>
                    Pengajuan Ditolak
                </div>
                <div class="decision-summary-note mt-2">USPK ini berstatus ditolak, sehingga belum ada kontraktor pemenang final.</div>
            @else
                <div class="decision-summary-title">
                    <i class="fas fa-hourglass-half"></i>
                    Proses Voting Masih Berjalan
                </div>
                <div class="decision-summary-note mt-2">
                    Keputusan final belum ditetapkan. Pemenang akhir akan muncul otomatis setelah approver level terakhir finalize.
                </div>
            @endif
        </div>
    </div>

    {{-- Tender Pembanding --}}
    <div class="card mb-4 modern-card">
        <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid rgba(0,0,0,0.05);">
            <div class="card-title" style="font-size: 16px; font-weight: 700;">
                <i class="fas fa-balance-scale" style="color: var(--warning); margin-right: 8px;"></i> Perbandingan & Voting Tender
            </div>
        </div>
        <div class="card-body" style="padding: 24px; background: rgba(0,0,0,0.01);">
            @if($uspk->tenders->count() > 0)
                <div class="tender-scroll-container">
                    @foreach($uspk->tenders as $index => $tender)
                        @php
                            $isFinalWinnerCard = $isVotingFinalized && $winnerTenderId > 0 && (int) $tender->id === $winnerTenderId && $uspk->status !== \Modules\ServiceAgreementSystem\Models\UspkSubmission::STATUS_REJECTED;
                            $isInitiallySelected = $isFinalWinnerCard || (!$isVotingFinalized && ($tender->is_selected || $index === 0));
                        @endphp
                        <div class="tender-wrapper">
                            {{-- Kartu Tender --}}
                            <label class="tender-card {{ $isFinalWinnerCard ? 'tender-card--winner' : '' }}" data-tender-card data-tender-id="{{ $tender->id }}">
                                <input type="radio" name="selected_tender_id" value="{{ $tender->id }}" class="tender-radio" style="position: absolute; opacity: 0; pointer-events: none;" {{ $isInitiallySelected ? 'checked' : '' }}>
                                
                                <div class="tender-header">
                                    <div>
                                        <div class="tender-subtitle">
                                            @if($isFinalWinnerCard)
                                                <i class="fas fa-trophy"></i> PEMENANG FINAL
                                            @elseif($tender->is_selected)
                                                <i class="fas fa-bookmark"></i> Rekomendasi Pengaju
                                            @else
                                                Kandidat
                                            @endif
                                        </div>
                                        <div class="tender-title">{{ $tender->contractor->name ?? '-' }}</div>
                                        <div class="tender-company">{{ $tender->contractor->company_name ?? '-' }}</div>
                                    </div>
                                    <div class="tender-radio-indicator">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>

                                <div class="price-duration-grid">
                                    <div class="pd-box price-box">
                                        <div class="pd-label">Harga Penawaran</div>
                                        <div class="pd-value">Rp {{ number_format($tender->tender_value, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="pd-box duration-box">
                                        <div class="pd-label">Estimasi Durasi</div>
                                        <div class="pd-value">{{ $tender->tender_duration ? $tender->tender_duration . ' hari' : '-' }}</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="input-label">Catatan Spesifikasi / Nego</div>
                                    <textarea class="form-control custom-textarea" rows="2" data-tender-description data-original-value="{{ $tender->description }}" readonly>{{ $tender->description }}</textarea>
                                </div>

                                <div class="edit-grid mb-3">
                                    <div>
                                        <label class="input-label">Edit Harga</label>
                                        <input type="number" class="form-control custom-input" data-tender-value data-original-value="{{ $tender->tender_value }}" value="{{ $tender->tender_value }}" step="0.01" min="0" disabled>
                                    </div>
                                    <div>
                                        <label class="input-label">Edit Durasi</label>
                                        <input type="number" class="form-control custom-input" data-tender-duration data-original-value="{{ $tender->tender_duration }}" value="{{ $tender->tender_duration }}" min="1" disabled>
                                    </div>
                                </div>

                                <div class="tender-footer">
                                    <div>
                                        @if($tender->attachment_path)
                                            <a href="{{ asset('storage/' . $tender->attachment_path) }}" target="_blank" class="attachment-btn">
                                                <i class="fas fa-paperclip"></i> File Lampiran
                                            </a>
                                        @else
                                            <span class="text-muted" style="font-size: 11px; font-style: italic;">Tidak ada lampiran</span>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-light btn-sm toggle-edit-btn" data-tender-edit-toggle>
                                        <i class="fas fa-pen"></i> Sesuaikan
                                    </button>
                                </div>
                            </label>

                            {{-- SECTION VOTING APPROVER --}}
                            @php
                                $tenderVoters = $uspk->approvals->filter(function($app) use ($tender) {
                                    return $app->voteTender && $app->voteTender->id == $tender->id;
                                });
                            @endphp
                            
                            <div class="voter-section">
                                <div class="voter-line"></div>
                                <div class="voter-title">VOTING APPROVER</div>
                                <div class="voters-container">
                                    @if($tenderVoters->count() > 0)
                                        @foreach($tenderVoters as $app)
                                            <div class="voter-tooltip" 
                                                 data-name="{{ $app->approver->name ?? 'Unknown' }}" 
                                                 data-level="{{ $app->level }}" 
                                                 data-comment="{{ $app->comment ?: 'Tanpa catatan' }}">
                                                <div class="voter-avatar">
                                                    {{ strtoupper(substr($app->approver->name ?? 'U', 0, 1)) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <span style="font-size: 11px; color: #a1a1aa; font-style: italic;">Belum ada vote</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-folder-open empty-icon"></i>
                    <p>Belum ada data tender yang dilampirkan.</p>
                </div>
            @endif
        </div>
    </div>

    @if($canProcessLegal || $uspk->hasFinalSpkDocument())
    <div class="card mb-4 modern-card">
        <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid rgba(0,0,0,0.05);">
            <div class="card-title" style="font-size: 16px; font-weight: 700;">
                <i class="fas fa-gavel" style="color: var(--accent); margin-right: 8px;"></i> Proses Legal SPK
            </div>
        </div>
        <div class="card-body" style="padding: 24px;">
            @if($uspk->hasFinalSpkDocument())
                <div class="alert-card mb-4">
                    <div class="card-body">
                        <i class="fas fa-check-circle info-icon" style="color: var(--success);"></i>
                        <div class="info-text">
                            Dokumen SPK final sudah diunggah oleh <strong>{{ $uspk->legalUploader->name ?? 'Legal' }}</strong>
                            @if($uspk->legal_spk_uploaded_at)
                                pada <strong>{{ $uspk->legal_spk_uploaded_at->format('d M Y H:i') }}</strong>
                            @endif.
                            @if($uspk->legal_spk_notes)
                                <div class="mt-2"><strong>Catatan Legal:</strong> {{ $uspk->legal_spk_notes }}</div>
                            @endif
                            @if(!$uspk->hasSubmitterSignedSpkDocument())
                                <div class="mt-2"><strong>Tahap berikutnya:</strong> Pengaju perlu upload ulang SPK yang sudah ditandatangani agar USPK masuk ke proses QC.</div>
                            @endif
                        </div>
                    </div>
                </div>

                @if(!$uspk->hasSubmitterSignedSpkDocument())
                    <div class="card mb-4" style="border: 1px solid #bfdbfe; border-radius: 12px; background: #f8fbff;">
                        <div class="card-body" style="padding: 16px;">
                            <h4 style="margin: 0 0 12px; font-size: 14px; font-weight: 700; color: #1e3a8a;">Upload SPK Bertanda Tangan Pengaju</h4>
                            <p class="text-muted" style="font-size: 12px; margin-bottom: 12px;">
                                Langkah ini wajib dilakukan setelah SPK Final dari Legal terbit, agar proses QC bisa dimulai.
                            </p>

                            @if($canUploadSignedSpk)
                                <form action="{{ route('sas.uspk-qc.upload-signed', $uspk) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label class="input-label">File SPK dengan TTD (PDF/DOC/DOCX)</label>
                                        <input type="file" name="signed_spk_document" class="form-control" accept=".pdf,.doc,.docx" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary action-btn">
                                        <i class="fas fa-file-signature"></i> Upload SPK dengan TTD 
                                    </button>
                                </form>
                            @else
                                <div class="alert" style="margin: 0; padding: 10px 12px; border-radius: 10px; background: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a; font-size: 12px;">
                                    Mode baca. Upload SPK TTD hanya bisa dilakukan oleh <strong>pengaju USPK</strong> atau <strong>admin</strong>.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <div class="alert-card mb-4">
                    <div class="card-body">
                        <i class="fas fa-info-circle info-icon"></i>
                        <div class="info-text">
                            USPK ini sudah approved final, namun dokumen SPK belum terbit. Export draft SPK, lakukan review/legal negotiation, lalu upload dokumen final yang sudah disepakati.
                        </div>
                    </div>
                </div>
            @endif

            @if($uspk->hasSubmitterSignedSpkDocument())
                <div class="alert-card mb-3">
                    <div class="card-body">
                        <i class="fas fa-file-signature info-icon" style="color: var(--success);"></i>
                        <div class="info-text">
                            SPK bertanda tangan sudah diunggah oleh <strong>{{ $uspk->submitterSignedUploader->name ?? 'Pengaju' }}</strong>
                            @if($uspk->submitter_signed_spk_uploaded_at)
                                pada <strong>{{ $uspk->submitter_signed_spk_uploaded_at->format('d M Y H:i') }}</strong>
                            @endif.
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $uspk->submitter_signed_spk_document_path) }}" target="_blank" class="attachment-btn">
                                    <i class="fas fa-download"></i> Lihat SPK dengan TTD
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($uspk->hasFinalSpkDocument())
                <div class="alert-card mb-3" style="border: 1px solid #fde68a; background: #fffbeb;">
                    <div class="card-body">
                        <i class="fas fa-exclamation-triangle info-icon" style="color: #b45309;"></i>
                        <div class="info-text" style="color: #78350f;">
                            <strong>SPK bertanda tangan pengaju belum diunggah.</strong>
                            Dokumen yang sudah ada saat ini adalah <strong>SPK Final dari Legal</strong>.
                            Tahap QC (penugasan verifier dan update progress) baru aktif setelah pengaju upload SPK TTD.
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if($canProcessLegal)
        <div class="bottom-split-grid" style="grid-template-columns: 1.2fr 0.8fr; gap: 16px;">
            <div class="card" style="border: 1px solid var(--border-color); border-radius: 12px;">
                <div class="card-body" style="padding: 16px;">
                    <h4 style="margin: 0 0 12px; font-size: 14px; font-weight: 700;">Upload SPK Final Dari Legal</h4>
                    <form action="{{ route('sas.uspk-legal.upload', $uspk) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="input-label">File SPK Final (PDF/DOC/DOCX)</label>
                            <input type="file" name="spk_document" class="form-control" accept=".pdf,.doc,.docx" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="input-label">Catatan Legal (Opsional)</label>
                            <textarea name="legal_spk_notes" class="form-control custom-textarea" rows="3" placeholder="Catatan kesepakatan final dengan kontraktor..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success action-btn">
                            <i class="fas fa-upload"></i> Upload SPK Final
                        </button>
                    </form>
                </div>
            </div>

            <div class="card" style="border: 1px solid #fecaca; border-radius: 12px;">
                <div class="card-body" style="padding: 16px;">
                    <h4 style="margin: 0 0 8px; font-size: 14px; font-weight: 700; color: #b91c1c;">Kembalikan ke Pemilihan</h4>
                    <p class="text-muted" style="font-size: 12px; margin-bottom: 12px;">Gunakan jika hasil nego/legal belum final dan perlu voting ulang approver final.</p>
                    <form action="{{ route('sas.uspk-legal.return', $uspk) }}" method="POST" onsubmit="return confirm('Kembalikan proses ke pemilihan kontraktor oleh approver final?')">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="input-label">Alasan Pengembalian</label>
                            <textarea name="comment" class="form-control custom-textarea" rows="3" placeholder="Tuliskan alasan wajib..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger action-btn">
                            <i class="fas fa-undo"></i> Kembalikan Proses
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

    </div>
    @endif

    @if($uspk->hasFinalSpkDocument() || $uspk->hasSubmitterSignedSpkDocument() || $qcStatus !== '')
    <div class="card mb-4 modern-card">
        <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid rgba(0,0,0,0.05);">
            <div class="card-title" style="font-size: 16px; font-weight: 700;">
                <i class="fas fa-clipboard-check" style="color: var(--info); margin-right: 8px;"></i> Proses QC USPK
            </div>
        </div>
        <div class="card-body" style="padding: 24px;">

            <!-- Stack Form Vertically instead of split grids -->
            <div class="vertical-stack" style="display: flex; flex-direction: column; gap: 24px;">
                
                {{-- STEP 2 SECTION --}}
                <div class="qc-section">
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 8px;">
                        <span class="badge {{ $qcAssignmentBadgeClass }}" style="font-size: 13px;">{{ $qcAssignmentBadgeText }}</span>
                        <h4 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--text-primary);">Penugasan Tim Verifikasi <span class="text-muted" style="font-weight: 600; font-size: 13px; margin-left: 6px;">(Tahap 2)</span></h4>
                    </div>
                    @if($uspk->qcAssigner)
                        <div style="margin-bottom: 12px; font-size: 12px; color: var(--text-muted);">
                            <i class="fas fa-check-circle text-success mx-1"></i> Ditugaskan oleh {{ $uspk->qcAssigner->name }}{{ $uspk->qc_assigned_at ? ' (' . $uspk->qc_assigned_at->format('d M Y H:i') . ')' : '' }}
                        </div>
                    @endif

                        @if($canAssignQcVerifiers)
                            <div class="card" style="border: 1px solid var(--border-color); border-radius: 12px;">
                                <div class="card-body" style="padding: 16px;">
                                    <h4 style="margin: 0 0 10px; font-size: 14px; font-weight: 700;">Penugasan Verifier QC</h4>
                                    <form action="{{ route('sas.uspk-qc.assign-verifiers', $uspk) }}" method="POST">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label class="input-label">Pilih User Verifier (bisa lebih dari satu)</label>
                                            <select name="verifier_ids[]" class="form-control select2-searchable" multiple required style="min-height: 140px;" {{ $disableVerifierEdit ? 'disabled' : '' }}>
                                                @foreach($assignableQcUsers as $qcUser)
                                                    <option value="{{ $qcUser->id }}" {{ $uspk->qcVerifications->contains('user_id', $qcUser->id) ? 'selected' : '' }}>
                                                        {{ $qcUser->name }}{{ $qcUser->position ? ' - ' . $qcUser->position : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($disableVerifierEdit)
                                                <div class="text-danger mt-2" style="font-size: 12px; font-weight: 500;"><i class="fas fa-exclamation-circle"></i> Verifier tidak dapat diubah karena salah satu verifier telah memberikan keputusan.</div>
                                            @endif
                                        </div>
                                        @if(!$disableVerifierEdit)
                                        <button type="submit" class="btn btn-primary action-btn">
                                            <i class="fas fa-user-check"></i> Simpan Penugasan Verifier
                                        </button>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        @elseif($uspk->qcVerifications->isEmpty() && !$hasSubmitterSignedSpk)
                            <div class="card" style="border: 1px solid #fde68a; border-radius: 12px; background: #fffbeb;">
                                <div class="card-body" style="padding: 16px; text-align: center;">
                                    <i class="fas fa-file-signature" style="font-size: 24px; color: #b45309; margin-bottom: 8px; display: block;"></i>
                                    <p style="margin: 0; color: #78350f; font-size: 12px;">
                                        Penugasan verifier belum bisa dilakukan karena SPK bertanda tangan pengaju belum diunggah.
                                    </p>
                                </div>
                            </div>
                        @elseif(!$canAssignQcVerifiers && $uspk->qcVerifications->isEmpty())
                            <div class="card" style="border: 1px solid #fecaca; border-radius: 12px; background: #fef2f2;">
                                <div class="card-body" style="padding: 16px; text-align: center;">
                                    <i class="fas fa-lock" style="font-size: 24px; color: #b91c1c; margin-bottom: 8px; display: block;"></i>
                                    <p style="margin: 0; color: #7f1d1d; font-size: 12px;">
                                        Mode baca. Hanya role QC Coordinator atau Admin yang bisa menambah verifier.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Deleted redundant deadline module -->

                        @if($uspk->qcVerifications->isNotEmpty())
                            <div class="card" style="border: 1px solid var(--border-color); border-radius: 12px; margin-top: 12px;">
                                <div class="card-body" style="padding: 16px;">
                                    <h4 style="margin: 0 0 10px; font-size: 14px; font-weight: 700;">Daftar Verifier QC</h4>
                                    <div class="table-wrapper">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Verifier</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($uspk->qcVerifications as $verification)
                                                    <tr>
                                                        <td>{{ $verification->verifier->name ?? '-' }}</td>
                                                        <td><span class="badge badge-{{ $verification->status }}">{{ ucfirst($verification->status) }}</span></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                {{-- END STEP 2 SECTION --}}

                {{-- STEP 3 SECTION --}}
                <div class="qc-section">
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 8px; margin-top: 16px;">
                        <span class="badge {{ $qcReportBadgeClass }}" style="font-size: 13px;">{{ $qcReportBadgeText }}</span>
                        <h4 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--text-primary);">Progress Pelaksanaan & Laporan <span class="text-muted" style="font-weight: 600; font-size: 13px; margin-left: 6px;">(Tahap 3)</span></h4>
                    </div>

                        @if($uspk->work_reported_completed_at)
                            <div style="margin-bottom: 12px; color: var(--text-muted); font-size: 12px;">
                                ✓ Dilaporkan selesai pada <strong>{{ $uspk->work_reported_completed_at->format('d M Y H:i') }}</strong>
                            </div>
                        @else
                            <div style="margin-bottom: 12px; padding: 10px 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 12px; color: #166534; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-info-circle"></i> Belum dilaporkan selesai
                            </div>
                        @endif

                        @if($totalBlocks > 0)
                            <div class="card" style="border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 12px;">
                                <div class="card-body" style="padding: 16px;">
                                    <h4 style="margin: 0 0 10px; font-size: 14px; font-weight: 700;">Rekap Progress Blok SPK</h4>
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;">
                                        <span class="badge" style="background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                            Progress {{ $completedBlocks }}/{{ $totalBlocks }} ({{ $blockProgressPercent }}%)
                                        </span>
                                        <span class="badge" style="background: {{ $overdueBlocks > 0 ? '#fee2e2' : '#ecfeff' }}; color: {{ $overdueBlocks > 0 ? '#b91c1c' : '#0f766e' }}; border: 1px solid {{ $overdueBlocks > 0 ? '#fecaca' : '#99f6e4' }};">
                                            Overdue: {{ $overdueBlocks }} blok
                                        </span>
                                        <span class="badge" style="background: {{ $dueSoonBlocks > 0 ? '#fef3c7' : '#ecfeff' }}; color: {{ $dueSoonBlocks > 0 ? '#b45309' : '#0f766e' }}; border: 1px solid {{ $dueSoonBlocks > 0 ? '#fde68a' : '#99f6e4' }};">
                                            Deadline ≤ 3 hari: {{ $dueSoonBlocks }} blok
                                        </span>
                                    </div>

                                    <div class="block-progress-bar" aria-label="Persentase progress blok">
                                        <div class="block-progress-bar-fill" style="width: {{ $blockProgressPercent }}%;"></div>
                                    </div>

                                    @if($canManageBlockCompletion || $canManageBlockDeadlines)
                                        @if($canManageBlockDeadlines)
                                        <div style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px dashed #cbd5e1; margin-top: 12px; margin-bottom: 12px; display: inline-flex; align-items: center; gap: 12px;">
                                            <span style="font-size: 12px; font-weight: 600; color: #475569;">Set Deadline Semua Blok:</span>
                                            <input type="date" id="global_deadline_setter" class="form-control" style="width: auto; padding: 6px 10px; font-size: 12px;">
                                            <button type="button" class="btn btn-secondary action-btn" onclick="applyGlobalDeadline()" style="font-size: 12px; padding: 6px 12px;">
                                                <i class="fas fa-check"></i> Terapkan
                                            </button>
                                            <script>
                                                function applyGlobalDeadline() {
                                                    const val = document.getElementById('global_deadline_setter').value;
                                                    if(val) document.querySelectorAll('.block-deadline-input').forEach(i => i.value = val);
                                                }
                                            </script>
                                        </div>
                                        @endif
                                        <form action="{{ route('sas.uspk-qc.block-progress', $uspk) }}" method="POST" style="margin-top: 12px;">
                                            @csrf
                                            <div class="table-wrapper">
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <th>Blok</th>
                                                            <th>Deadline</th>
                                                            <th>Selesai</th>
                                                            <th>Tgl Selesai</th>
                                                            <th>Updated By</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($uspkBlocks as $block)
                                                            @php
                                                                $progress = $blockProgressById->get((int) $block->id);
                                                                $isCompleted = (string) optional($progress)->status === \Modules\ServiceAgreementSystem\Models\UspkBlockProgress::STATUS_COMPLETED;
                                                                $deadlineDate = optional(optional($progress)->deadline_at)->format('Y-m-d');
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    <input type="hidden" name="block_ids[]" value="{{ $block->id }}">
                                                                    <strong>{{ $block->name }}</strong>
                                                                    @if($block->code)
                                                                        <div style="font-size: 11px; color: var(--text-muted);">{{ $block->code }}</div>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($canManageBlockDeadlines)
                                                                        <input type="date" name="deadline_at[{{ $block->id }}]" value="{{ $deadlineDate }}" class="form-control block-deadline-input" style="min-width: 130px; font-size: 12px; padding: 8px;">
                                                                    @else
                                                                        <span style="font-size: 12px;">{{ optional(optional($progress)->deadline_at)->format('d M Y') ?? '-' }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($canManageBlockCompletion)
                                                                        <label style="display: inline-flex; align-items: center; gap: 6px; margin: 0; font-size: 12px; font-weight: 600;">
                                                                            <input type="checkbox" name="completed_blocks[]" value="{{ $block->id }}" {{ $isCompleted ? 'checked' : '' }}>
                                                                            Selesai
                                                                        </label>
                                                                    @else
                                                                        <span class="badge {{ $isCompleted ? 'badge-approved' : 'badge-pending_assignment' }}">
                                                                            {{ $isCompleted ? 'Selesai' : 'Belum Selesai' }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td style="font-size: 12px;">{{ optional(optional($progress)->completed_at)->format('d M Y H:i') ?? '-' }}</td>
                                                                <td style="font-size: 12px;">{{ optional(optional($progress)->completedBy)->name ?? '-' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <p class="text-muted" style="font-size: 12px; margin: 10px 0 0;">Data blok diisi secara kolektif berdasakan akses peran Anda.</p>
                                            <div style="display: flex; gap: 12px; align-items: center; margin-top: 10px; flex-wrap: wrap;">
                                                <button type="submit" class="btn btn-primary action-btn">
                                                    <i class="fas fa-save"></i> Simpan Data Blok
                                                </button>
                                                
                                                @if($canReportWorkCompleted)
                                                    <a href="#" onclick="event.preventDefault(); document.getElementById('report-completed-form').submit();" class="btn btn-success action-btn" style="background-color: var(--success); color: white; border-color: var(--success);">
                                                        <i class="fas fa-flag-checkered"></i> Laporkan Pekerjaan Selesai
                                                    </a>
                                                @elseif(!$uspk->work_reported_completed_at && $totalBlocks > 0 && $completedBlocks < $totalBlocks)
                                                    <span style="font-size: 12px; color: #9a3412; font-weight: 600; padding: 8px 12px; border-radius: 8px; background: #fff7ed; border: 1px dashed #fed7aa;">
                                                        <i class="fas fa-info-circle"></i> Selesaikan ke-{{ $totalBlocks }} blok untuk menyelesaikan
                                                    </span>
                                                @endif
                                            </div>
                                        </form>
                                        
                                        @if($canReportWorkCompleted)
                                        <form id="report-completed-form" action="{{ route('sas.uspk-qc.report-completed', $uspk) }}" method="POST" style="display: none;" onsubmit="return confirm('Lanjut kirim laporan pekerjaan selesai untuk proses verifikasi QC?')">
                                            @csrf
                                        </form>
                                        @endif
                                    @else
                                        <div class="alert" style="margin-top: 12px; padding: 10px 12px; border-radius: 10px; background: #fef3c7; border: 1px solid #fde68a; color: #78350f; font-size: 12px;">
                                            Mode baca: Data blok belum ada atau tidak dapat Anda ubah.
                                        </div>
                                        <div class="table-wrapper" style="margin-top: 12px;">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>Blok</th>
                                                        <th>Status</th>
                                                        <th>Deadline</th>
                                                        <th>Tgl Selesai</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($uspkBlocks as $block)
                                                        @php
                                                            $progress = $blockProgressById->get((int) $block->id);
                                                            $isCompleted = (string) optional($progress)->status === \Modules\ServiceAgreementSystem\Models\UspkBlockProgress::STATUS_COMPLETED;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $block->name }}</td>
                                                            <td>
                                                                <span class="badge {{ $isCompleted ? 'badge-approved' : 'badge-pending_assignment' }}">
                                                                    {{ $isCompleted ? 'Selesai' : 'Belum Selesai' }}
                                                                </span>
                                                            </td>
                                                            <td>{{ optional(optional($progress)->deadline_at)->format('d M Y') ?? '-' }}</td>
                                                            <td>{{ optional(optional($progress)->completed_at)->format('d M Y H:i') ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="card" style="border: 1px solid #fee2e2; border-radius: 12px; background: #fef2f2; margin-bottom: 12px;">
                                <div class="card-body" style="padding: 16px; text-align: center;">
                                    <i class="fas fa-inbox" style="font-size: 24px; color: #b91c1c; margin-bottom: 8px; display: block;"></i>
                                    <p style="margin: 0; color: #7f1d1d; font-size: 12px;">
                                        Belum ada blok dalam SPK ini. Silakan atur blok dan deadline di bagian atas.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Riwayat Proses QC dipindahkan ke layout bawah -->

                        <!-- Moved Laporkan Pekerjaan Selesai into table -->

                        @if($canVerifyQc)
                            <div class="card" style="border: 1px solid var(--border-color); border-radius: 12px; margin-top: 12px;">
                                <div class="card-body" style="padding: 16px;">
                                    <h4 style="margin: 0 0 10px; font-size: 14px; font-weight: 700;">Verifikasi Pekerjaan (Tugas Anda)</h4>
                                    <form action="{{ route('sas.uspk-qc.verify', $uspk) }}" method="POST">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label class="input-label">Catatan Verifikasi</label>
                                            <textarea name="comment" class="form-control custom-textarea" rows="3" placeholder="Tuliskan hasil pengecekan Anda..."></textarea>
                                        </div>
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <button type="submit" name="action" value="approved" class="btn btn-success action-btn">
                                                <i class="fas fa-check-circle"></i> Approve Verifikasi
                                            </button>
                                            <button type="submit" name="action" value="rejected" class="btn btn-danger action-btn">
                                                <i class="fas fa-times-circle"></i> Tolak (Butuh Revisi)
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                </div>
                {{-- END STEP 3 SECTION --}}
            </div>
        </div>
    </div>
    @endif

    @php
        // Hierarchical approval: Only the minimum level with pending/on_hold status is active.
        // Higher levels cannot act until their preceding level is completed (approved/rejected).
        $minPendingLevel = $uspk->approvals
            ->whereIn('status', ['pending', 'on_hold'])
            ->min('level');
        
        // Get approval at the active (minimum) level
        $currentApproval = $minPendingLevel !== null 
            ? $uspk->approvals->firstWhere('level', $minPendingLevel) 
            : null;
        
        $currentStepAssignee = null;
        if ($currentApproval) {
            $step = optional($currentApproval->schema)->steps?->firstWhere('level', $currentApproval->level);
            $currentStepAssignee = $step?->user;
        }
        $currentApproverId = (int) ($currentStepAssignee->id ?? $currentApproval?->user_id ?? 0);
        $isApprovalActionAllowed = $currentApproval && ($isSasAdmin || $currentApproverId === (int) auth()->id());
        $actionableApproval = $isApprovalActionAllowed ? $currentApproval : null;
        $maxApprovalLevel = $uspk->approvals->max('level');
        $isFinalApprovalLevel = $actionableApproval && (int) $actionableApproval->level === (int) $maxApprovalLevel;

        $rolledBackApproval = $isSasAdmin
            ? $uspk->approvals
                ->whereIn('status', ['approved', 'rejected', 'on_hold'])
                ->sortByDesc('approved_at')
                ->first()
            : null;
    @endphp


    {{-- Keputusan Anda Form --}}
    <div class="approval-action-wrapper mb-4">
        @if($uspk->approvals->count() > 0)


                @if($actionableApproval)
                <div class="card modern-card highlight-card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="card-title mb-0" style="font-size: 18px; font-weight: 700;">
                                <i class="fas fa-gavel text-accent me-2"></i> Form Keputusan Anda
                            </div>
                            @if($isFinalApprovalLevel)
                                <span class="badge badge-success px-3 py-2" style="border-radius: 8px;">Level Final</span>
                            @else
                                <span class="badge badge-warning px-3 py-2" style="border-radius: 8px;">Level {{ $actionableApproval->level }} Voting</span>
                            @endif
                        </div>
                        
                        <p class="text-muted" style="font-size: 13px; margin-bottom: 20px;">
                            Silakan pilih kartu tender di atas yang menjadi rekomendasi Anda. Keputusan akhir mutlak berada pada approver level tertinggi.
                        </p>
                        @if($isSasAdmin)
                            <div class="alert alert-success" style="margin-bottom: 16px;">
                                <i class="fas fa-user-shield"></i> Mode Admin Override aktif. Anda dapat memproses approval atas nama approver pada level aktif.
                            </div>
                        @endif

                        <form id="approvalActionForm" action="{{ route('sas.uspk.approve', $uspk) }}" method="POST">
                            @csrf
                            <input type="hidden" name="selected_tender_id" id="selectedTenderId" value="">
                            <input type="hidden" name="vote_tender_value" id="voteTenderValue">
                            <input type="hidden" name="vote_tender_duration" id="voteTenderDuration">
                            <input type="hidden" name="vote_tender_description" id="voteTenderDescription">
                            
                            <div class="form-group mb-4">
                                <label class="input-label" style="font-size: 12px;">Alasan & Catatan Keputusan</label>
                                <textarea name="comment" id="approvalComment" class="form-control custom-textarea" rows="4" placeholder="Sebutkan alasan Anda memilih tender tersebut..."></textarea>
                            </div>

                            <div class="action-buttons-group">
                                <button type="submit" class="btn btn-success action-btn" id="approveBtn" formaction="{{ route('sas.uspk.approve', $uspk) }}">
                                    <i class="fas fa-check-circle"></i> {{ $isFinalApprovalLevel ? 'Approve & Finalize' : 'Approve & Vote' }}
                                </button>
                                <button type="submit" class="btn btn-warning text-dark action-btn" id="holdBtn" formaction="{{ route('sas.uspk.hold', $uspk) }}">
                                    <i class="fas fa-pause-circle"></i> Hold Review
                                </button>
                                <button type="submit" class="btn btn-danger action-btn" id="rejectBtn" formaction="{{ route('sas.uspk.reject', $uspk) }}">
                                    <i class="fas fa-times-circle"></i> Reject
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Rollback button moved to right column header -->

                @if(!$actionableApproval && $currentApproval)
                <div class="card alert-card">
                    <div class="card-body">
                        <i class="fas fa-info-circle info-icon"></i>
                        <div class="info-text">
                            Tahap approval saat ini sedang diproses pada <strong>Level {{ $currentApproval->level }}</strong> oleh <strong>{{ $currentStepAssignee->name ?? $currentApproval->approver->name ?? 'Approver terkait' }}</strong>.
                        </div>
                    </div>
                </div>
                @endif
            @else
                <div class="card alert-card">
                    <div class="card-body" style="justify-content: center; text-align: center; flex-direction: column;">
                        <i class="fas fa-file-signature info-icon" style="opacity: 0.5; margin-bottom: 8px;"></i>
                        <div class="info-text text-muted">
                            Proses approval akan dimulai setelah USPK disubmit.
                        </div>
                    </div>
                </div>
            @endif
    </div>

    {{-- Layout Split: Riwayat QC di Kiri, Riwayat Approval di Kanan --}}
    <div class="bottom-split-grid mb-4">
        
        {{-- KOLOM KIRI: Riwayat Proses QC --}}
        <div class="history-column-qc">
            <div class="card modern-card" style="height: 100%;">
                <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <div class="card-title" style="font-size: 16px; font-weight: 700;">
                        <i class="fas fa-tasks text-primary" style="margin-right: 8px;"></i> Riwayat Proses QC (Permanen)
                    </div>
                </div>
                <div class="card-body p-4">
                    @if($importantQcHistoryLogs->isNotEmpty())
                        <div class="modern-timeline">
                            @foreach($importantQcHistoryLogs as $qcLog)
                            <div class="timeline-item">
                                <div class="timeline-marker" style="background:#475569;"></div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="timeline-name">{{ $qcLog->actor->name ?? '-' }}</span>
                                            <span class="badge" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; font-weight: 700;">
                                                {{ $qcHistoryActionLabels[$qcLog->action] ?? ucfirst(str_replace('_', ' ', (string) $qcLog->action)) }}
                                            </span>
                                        </div>
                                        <span class="timeline-date">
                                            <i class="far fa-clock"></i> {{ optional($qcLog->created_at)->format('d M Y H:i') ?? '-' }}
                                        </span>
                                    </div>
                                    @if((string) $qcLog->action === 'verifier_decision_recorded' && $qcLog->verification && $qcLog->verification->verifier)
                                        <div class="timeline-role mt-1" style="font-size: 11px;">Verifier: <strong>{{ $qcLog->verification->verifier->name }}</strong></div>
                                    @endif
                                    @if($qcLog->status_before || $qcLog->status_after)
                                        <div class="timeline-role mt-1" style="font-size: 11px;">Siklus: <span style="font-weight: 600;">{{ $qcLog->status_before ?: '-' }} <i class="fas fa-arrow-right mx-1" style="color:var(--text-muted); font-size:10px;"></i> {{ $qcLog->status_after ?: '-' }}</span></div>
                                    @endif
                                    @if($qcLog->comment)
                                        <div class="timeline-comment mt-2">
                                            <i class="fas fa-quote-left quote-icon"></i>
                                            {{ $qcLog->comment }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state" style="padding: 20px;">
                            <i class="fas fa-inbox empty-icon" style="font-size: 30px;"></i>
                            <p style="font-size: 13px;">Belum ada riwayat QC.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Approval Timeline --}}
        <div class="history-column">
            <div class="card modern-card" style="height: 100%;">
                <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;">
                    <div class="card-title" style="font-size: 16px; font-weight: 700; margin: 0;">
                        <i class="fas fa-history text-success" style="margin-right: 8px;"></i> Riwayat Jenjang Approval
                    </div>
                    @if($canRollbackApproval && $highestActiveApproval)
                        <form action="{{ route('sas.uspk.rollback-approval', $uspk) }}" method="POST" onsubmit="return confirm('Yakin ingin merubah keputusan pada level {{ $highestActiveApproval->level }}? Keputusan Anda akan dikembalikan ke status Pending.');" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="approval_id" value="{{ $highestActiveApproval->id }}">
                            <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm" title="Ubah Keputusan Level {{ $highestActiveApproval->level }}" style="border-radius: 6px; font-size: 12px; font-weight: 700; padding: 6px 12px; color: #dc3545; border-color: #dc3545;">
                                <i class="fas fa-undo"></i> Ubah Keputusan
                            </button>
                        </form>
                    @endif
                </div>
                <div class="card-body p-4">
                    @if($uspk->approvals->count() > 0)
                        <div class="modern-timeline">
                            @foreach($uspk->approvals as $approval)
                            @php
                                $rawComment = (string) ($approval->comment ?? '');
                                $adminProxyLabel = null;
                                $cleanComment = $rawComment;

                                if (preg_match('/^\[Diproses oleh admin:\s*(.*?)\]\s*(.*)$/u', $rawComment, $matches)) {
                                    $adminProxyLabel = trim((string) ($matches[1] ?? 'Admin'));
                                    $cleanComment = trim((string) ($matches[2] ?? ''));
                                }
                            @endphp
                            <div class="timeline-item">
                                <div class="timeline-marker {{ $approval->status }}"></div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="timeline-name">{{ $approval->approver->name ?? 'Unknown' }}</span>
                                            <span class="badge badge-{{ $approval->status }} timeline-badge">
                                                {{ ucfirst($approval->status) }}
                                            </span>
                                            @if($adminProxyLabel)
                                                <span class="admin-proxy-badge" title="Aksi diproses oleh admin atas nama approver">
                                                    On behalf by Admin: {{ $adminProxyLabel }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="timeline-date">
                                            <i class="far fa-clock"></i> {{ $approval->approved_at ? $approval->approved_at->format('d M Y H:i') : 'Menunggu' }}
                                        </span>
                                    </div>
                                    <div class="timeline-role">Level {{ $approval->level }} · {{ $approval->approver->position ?? 'Approver' }}</div>
                                    
                                    @if($approval->voteTender)
                                        <div class="vote-summary-box mt-2">
                                            <div class="vote-title"><i class="fas fa-vote-yea text-primary me-1"></i> Memilih: <strong>{{ $approval->voteTender->contractor->name ?? '-' }}</strong></div>
                                            <div class="vote-details">Nego: Rp {{ number_format((float) ($approval->vote_tender_value ?? $approval->voteTender->tender_value), 0, ',', '.') }} | {{ $approval->vote_tender_duration ?? $approval->voteTender->tender_duration ?? '-' }} Hari</div>
                                        </div>
                                    @endif

                                    @if($cleanComment !== '')
                                        <div class="timeline-comment mt-2">
                                            <i class="fas fa-quote-left quote-icon"></i>
                                            {{ $cleanComment }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state" style="padding: 20px;">
                            <i class="fas fa-project-diagram empty-icon" style="font-size: 30px;"></i>
                            <p style="font-size: 13px;">Belum ada riwayat tercatat.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <style>
        /* === GLOBAL VARS & OVERRIDES === */
        :root {
            --primary: #4f46e5;
            --primary-light: #e0e7ff;
            --accent: #6366f1;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        body { background-color: var(--bg-body); color: var(--text-secondary); }

        /* === MODERN CARD === */
        .modern-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            transition: var(--transition);
        }
        .highlight-card { border: 1px solid var(--accent); box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.1); }
        .desc-box { background: #f1f5f9; padding: 16px; border-radius: 12px; font-size: 14px; line-height: 1.6; color: var(--text-secondary); border-left: 4px solid var(--text-muted); }

        /* === INFO GRID === */
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .info-item { background: #f8fafc; padding: 14px 16px; border-radius: 12px; border: 1px solid var(--border-color); }
        .info-label { font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted); letter-spacing: 0.5px; margin-bottom: 6px; }
        .info-value { font-size: 14px; font-weight: 600; color: var(--text-primary); }
        
        .tags-container { display: flex; flex-wrap: wrap; gap: 6px; }
        .tag-badge { background: var(--primary-light); color: var(--primary); padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
        .status-badge { padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .decision-summary-card { border-width: 2px; }
        .decision-summary-card--final {
            border-color: rgba(16, 185, 129, 0.35);
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(5, 150, 105, 0.03));
        }
        .decision-summary-card--progress {
            border-color: rgba(245, 158, 11, 0.35);
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.09), rgba(245, 158, 11, 0.02));
        }
        .decision-summary-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 18px;
            font-weight: 800;
            color: var(--text-primary);
        }
        .decision-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }
        .decision-label {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--text-muted);
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .decision-value {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1.3;
        }
        .decision-subvalue {
            margin-top: 2px;
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 600;
        }
        .decision-summary-note {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.5;
            font-weight: 600;
        }
        .admin-proxy-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 999px;
            background: #ecfeff;
            border: 1px solid #a5f3fc;
            color: #0e7490;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.2px;
            white-space: nowrap;
        }
        .qc-split-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }
        @media (max-width: 992px) {
            .qc-split-grid { grid-template-columns: 1fr; }
        }
        .qc-collapsible {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
        }
        .qc-collapsible-summary {
            list-style: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            font-weight: 700;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-color);
        }
        .qc-collapsible-summary::-webkit-details-marker { display: none; }
        .qc-summary-text {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
        }
        .qc-collapsible-body {
            padding: 14px 16px 16px;
            background: #fcfdff;
        }
        .block-progress-bar {
            width: 100%;
            height: 12px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
        }
        .block-progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #0891b2, #22c55e);
            border-radius: inherit;
            transition: width 0.25s ease;
        }

        /* === SPLIT LAYOUT GRID === */
        .bottom-split-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: start;
        }
        @media (max-width: 992px) {
            .bottom-split-grid { grid-template-columns: 1fr; }
        }

        /* === TENDER COMPARISON === */
        .tender-scroll-container { display: flex; gap: 20px; overflow-x: auto; padding-bottom: 20px; padding-top: 5px; scroll-snap-type: x proximity; }
        .tender-scroll-container::-webkit-scrollbar { height: 8px; }
        .tender-scroll-container::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .tender-scroll-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        
        .tender-wrapper { display: flex; flex-direction: column; min-width: 340px; max-width: 360px; scroll-snap-align: start; flex: 0 0 auto; }
        
        .tender-card {
            background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 16px; padding: 20px;
            cursor: pointer; transition: var(--transition); position: relative; display: flex; flex-direction: column;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02); height: 100%;
        }
        .tender-card:hover { border-color: #cbd5e1; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        .tender-card--selected { border-color: var(--success) !important; background: rgba(16, 185, 129, 0.02); box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1) !important; }
        .tender-card--selected .tender-radio-indicator { background: var(--success); color: white; border-color: var(--success); }
        .tender-card--winner {
            border-color: #22c55e !important;
            background: linear-gradient(180deg, rgba(34, 197, 94, 0.12), rgba(34, 197, 94, 0.02));
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.15), 0 12px 20px -12px rgba(22, 163, 74, 0.55) !important;
        }
        .tender-card--winner .tender-subtitle {
            color: #166534;
            font-weight: 800;
        }
        .tender-card--winner .tender-radio-indicator {
            background: #22c55e;
            border-color: #22c55e;
            color: #ffffff;
        }
        
        .tender-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; gap: 12px; }
        .tender-subtitle { font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; letter-spacing: 0.5px; }
        .tender-title { font-size: 18px; font-weight: 800; color: var(--text-primary); line-height: 1.2; }
        .tender-company { font-size: 13px; color: var(--text-muted); margin-top: 4px; }
        
        .tender-radio-indicator {
            width: 24px; height: 24px; border-radius: 50%; border: 2px solid var(--border-color); display: flex;
            align-items: center; justify-content: center; color: transparent; transition: var(--transition); flex-shrink: 0;
        }
        .tender-radio-indicator i { font-size: 12px; }

        .price-duration-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
        .pd-box { padding: 12px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05); }
        .price-box { background: rgba(37,99,235,0.04); border-color: rgba(37,99,235,0.1); }
        .duration-box { background: rgba(16,185,129,0.04); border-color: rgba(16,185,129,0.1); }
        .pd-label { font-size: 10px; text-transform: uppercase; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; }
        .pd-value { font-size: 14px; font-weight: 800; color: var(--text-primary); }

        .input-label { font-size: 11px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; display: block; text-transform: uppercase; letter-spacing: 0.3px; }
        .custom-textarea, .custom-input { font-size: 13px; border-radius: 8px; border: 1px solid var(--border-color); padding: 10px 12px; background: #f8fafc; transition: var(--transition); }
        .custom-textarea:focus, .custom-input:focus { background: white; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); outline: none; }
        .custom-textarea:disabled, .custom-input:disabled, .custom-textarea[readonly] { background: #f1f5f9; cursor: not-allowed; opacity: 0.8; }
        
        .edit-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .tender-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 16px; border-top: 1px dashed var(--border-color); }
        .attachment-btn { font-size: 12px; font-weight: 600; color: var(--primary); text-decoration: none; padding: 6px 10px; border-radius: 6px; background: var(--primary-light); transition: var(--transition); }
        .attachment-btn:hover { background: var(--primary); color: white; text-decoration: none; }
        
        .toggle-edit-btn { font-size: 12px; font-weight: 600; border-radius: 6px; }

        /* === VOTER SECTION (THE MAGIC SAUCE) === */
        .voter-section { position: relative; margin-top: -10px; display: flex; flex-direction: column; align-items: center; z-index: 5; }
        .voter-line { width: 2px; height: 20px; background: var(--border-color); margin-bottom: 4px; }
        .voter-title { font-size: 9px; font-weight: 800; color: var(--text-muted); letter-spacing: 1px; background: var(--bg-body); padding: 0 8px; margin-bottom: 8px; z-index: 2; }
        .voters-container { display: flex; flex-wrap: wrap; justify-content: center; gap: -8px; background: var(--bg-card); padding: 6px 12px; border-radius: 20px; border: 1px solid var(--border-color); box-shadow: 0 2px 4px rgba(0,0,0,0.03); min-width: 80px; min-height: 40px; align-items: center;}
        
        .voter-tooltip { position: relative; cursor: pointer; margin-right: -8px; transition: transform 0.2s; }
        .voter-tooltip:hover { transform: translateY(-3px); z-index: 10; }
        .voter-tooltip:last-child { margin-right: 0; }
        
        .voter-avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--accent)); color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; border: 2px solid var(--bg-card); box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        /* Tooltip CSS Magic */
        .voter-tooltip::before, .voter-tooltip::after { opacity: 0; visibility: hidden; position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%) translateY(10px); transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); pointer-events: none; z-index: 100; }
        .voter-tooltip::before { content: ''; border: 6px solid transparent; border-top-color: #1e293b; margin-bottom: -11px; }
        .voter-tooltip::after {
            content: attr(data-name) " (Level " attr(data-level) ")\A\A" attr(data-comment);
            background: #1e293b; color: #f8fafc; padding: 10px 14px; border-radius: 8px; font-size: 12px;
            white-space: pre-wrap; width: max-content; max-width: 240px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2);
            text-align: left; line-height: 1.4; margin-bottom: 1px; font-family: inherit; font-weight: 500;
        }
        .voter-tooltip:hover::before, .voter-tooltip:hover::after { opacity: 1; visibility: visible; transform: translateX(-50%) translateY(-5px); }

        /* === ALERT / INFO CARD === */
        .alert-card { background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 12px; height: 100%; }
        .alert-card .card-body { display: flex; align-items: center; gap: 16px; padding: 16px 20px; }
        .info-icon { font-size: 24px; color: #0284c7; }
        .info-text { font-size: 14px; color: #0c4a6e; line-height: 1.5; }

        /* === TIMELINE === */
        .modern-timeline { position: relative; padding-left: 24px; }
        .modern-timeline::before { content: ''; position: absolute; top: 0; bottom: 0; left: 6px; width: 2px; background: var(--border-color); border-radius: 2px; }
        .timeline-item { position: relative; margin-bottom: 24px; }
        .timeline-item:last-child { margin-bottom: 0; }
        .timeline-marker { position: absolute; left: -24px; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: var(--text-muted); border: 3px solid var(--bg-card); box-shadow: 0 0 0 2px var(--border-color); }
        .timeline-marker.approved { background: var(--success); box-shadow: 0 0 0 2px rgba(16,185,129,0.2); }
        .timeline-marker.rejected { background: var(--danger); box-shadow: 0 0 0 2px rgba(239,68,68,0.2); }
        .timeline-marker.pending { background: var(--warning); box-shadow: 0 0 0 2px rgba(245,158,11,0.2); }
        
        .timeline-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px; }
        .timeline-name { font-size: 14px; font-weight: 700; color: var(--text-primary); }
        .timeline-date { font-size: 12px; color: var(--text-muted); font-weight: 500; }
        .timeline-badge { font-size: 10px; padding: 4px 8px; border-radius: 6px; }
        .timeline-role { font-size: 12px; color: var(--text-muted); margin-bottom: 8px; }
        
        .vote-summary-box { background: #f8fafc; border: 1px solid var(--border-color); padding: 10px 14px; border-radius: 8px; font-size: 13px; }
        .vote-title { color: var(--text-secondary); margin-bottom: 2px; }
        .vote-details { font-weight: 600; color: var(--text-primary); }
        
        .timeline-comment { background: rgba(0,0,0,0.03); padding: 12px 16px; border-radius: 0 12px 12px 12px; font-size: 13px; color: var(--text-secondary); position: relative; font-style: italic; border-left: 3px solid var(--accent); }
        .quote-icon { font-size: 10px; opacity: 0.3; margin-right: 6px; vertical-align: super; }

        /* BUTTONS */
        .action-btn { padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; transition: var(--transition); }
        .action-btn:hover { transform: translateY(-1px); }
        .action-buttons-group { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 20px; }

        /* EMPTY STATE */
        .empty-state { text-align: center; padding: 40px 20px; color: var(--text-muted); }
        .empty-icon { font-size: 40px; opacity: 0.3; margin-bottom: 16px; }
    </style>

    <script>
        function normalizeValue(value) { return (value ?? '').toString().trim(); }

        function cardHasNegotiationChanges(card) {
            if (!card) return false;
            const valueInput = card.querySelector('[data-tender-value]');
            const durationInput = card.querySelector('[data-tender-duration]');
            const descriptionInput = card.querySelector('[data-tender-description]');

            const valueChanged = normalizeValue(valueInput?.value) !== normalizeValue(valueInput?.dataset.originalValue);
            const durationChanged = normalizeValue(durationInput?.value) !== normalizeValue(durationInput?.dataset.originalValue);
            const descriptionChanged = normalizeValue(descriptionInput?.value) !== normalizeValue(descriptionInput?.dataset.originalValue);

            return valueChanged || durationChanged || descriptionChanged;
        }

        function setCardEditMode(card, editable) {
            const valueInput = card.querySelector('[data-tender-value]');
            const durationInput = card.querySelector('[data-tender-duration]');
            const descriptionInput = card.querySelector('[data-tender-description]');
            const toggleButton = card.querySelector('[data-tender-edit-toggle]');

            if (valueInput) valueInput.disabled = !editable;
            if (durationInput) durationInput.disabled = !editable;
            if (descriptionInput) descriptionInput.readOnly = !editable;

            if (toggleButton) {
                toggleButton.innerHTML = editable ? '<i class="fas fa-lock"></i> Kunci Edit' : '<i class="fas fa-pen"></i> Sesuaikan';
                toggleButton.classList.toggle('btn-warning', editable);
                toggleButton.classList.toggle('btn-light', !editable);
            }
        }

        function getSelectedTenderCard() {
            return document.querySelector('.tender-radio:checked')?.closest('[data-tender-card]') || null;
        }

        function syncTenderVoteFields() {
            const selectedCard = getSelectedTenderCard();
            const tenderValue = document.getElementById('voteTenderValue');
            const tenderDuration = document.getElementById('voteTenderDuration');
            const tenderDescription = document.getElementById('voteTenderDescription');
            const selectedTenderId = document.getElementById('selectedTenderId');

            if (!selectedCard) {
                if(tenderValue) tenderValue.value = '';
                if(tenderDuration) tenderDuration.value = '';
                if(tenderDescription) tenderDescription.value = '';
                if(selectedTenderId) selectedTenderId.value = '';
                return;
            }

            if(tenderValue) tenderValue.value = selectedCard.querySelector('[data-tender-value]')?.value || '';
            if(tenderDuration) tenderDuration.value = selectedCard.querySelector('[data-tender-duration]')?.value || '';
            if(tenderDescription) tenderDescription.value = selectedCard.querySelector('[data-tender-description]')?.value || '';
            if(selectedTenderId) selectedTenderId.value = selectedCard.querySelector('.tender-radio')?.value || '';
        }

        function validateApprovalAction(event) {
            const submitter = event.submitter;
            const decision = submitter ? submitter.id : '';
            const comment = document.getElementById('approvalComment')?.value.trim();
            const selectedTenderId = document.getElementById('selectedTenderId')?.value;

            if (decision === 'rejectBtn' && !comment) {
                event.preventDefault();
                alert('Komentar wajib diisi jika Anda ingin melakukan Reject.');
                return false;
            }

            if ((decision === 'approveBtn' || decision === 'holdBtn') && !selectedTenderId) {
                event.preventDefault();
                alert('Pilih salah satu kartu tender (klik kartunya) sebagai rekomendasi sebelum menyetujui.');
                return false;
            }

            if (decision === 'approveBtn' || decision === 'holdBtn') {
                const selectedCard = getSelectedTenderCard();
                if (cardHasNegotiationChanges(selectedCard)) {
                    if (!confirm('Anda melakukan perubahan angka nego/catatan pada tender ini. Perubahan akan disimpan. Lanjutkan?')) {
                        event.preventDefault(); return false;
                    }
                }
            }
            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const approvalForm = document.getElementById('approvalActionForm');
            const tenderCards = document.querySelectorAll('.tender-card');
            const tenderInputs = document.querySelectorAll('[data-tender-value], [data-tender-duration], [data-tender-description]');
            const editToggles = document.querySelectorAll('[data-tender-edit-toggle]');

            tenderCards.forEach(card => setCardEditMode(card, false));

            tenderCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    if (e.target.closest('[data-tender-edit-toggle]') || e.target.closest('input:not([type="radio"])') || e.target.closest('textarea') || e.target.closest('a')) return;

                    const radio = this.querySelector('.tender-radio');
                    if (radio) radio.checked = true;
                    
                    tenderCards.forEach(c => c.classList.remove('tender-card--selected'));
                    this.classList.add('tender-card--selected');
                    syncTenderVoteFields();
                });
            });

            tenderInputs.forEach(input => {
                input.addEventListener('input', syncTenderVoteFields);
                input.addEventListener('change', syncTenderVoteFields);
            });

            editToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const card = this.closest('[data-tender-card]');
                    if (!card) return;
                    
                    const isEditable = this.classList.contains('btn-warning');
                    setCardEditMode(card, !isEditable);
                    if (!isEditable) card.querySelector('[data-tender-value]')?.focus();
                });
            });

            // Set default selected jika ada
            const defaultSelected = document.querySelector('.tender-radio:checked');
            if (!defaultSelected && tenderCards.length > 0) {
                tenderCards[0].querySelector('.tender-radio').checked = true;
                tenderCards[0].classList.add('tender-card--selected');
            } else if (defaultSelected) {
                defaultSelected.closest('.tender-card').classList.add('tender-card--selected');
            }
            syncTenderVoteFields();

            if (approvalForm) approvalForm.addEventListener('submit', validateApprovalAction);
        });
    </script>
    @endpush
</x-serviceagreementsystem::layouts.master>