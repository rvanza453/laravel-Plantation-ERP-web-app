<x-serviceagreementsystem::layouts.master :title="'Kotak Tugas & Persetujuan'">
    
    <div class="mb-6">
        <h2 style="font-size: 24px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px;">Daftar Tugas Anda</h2>
        <p class="text-muted" style="font-size: 14px;">Selesaikan semua tugas persetujuan dan verifikasi untuk memastikan kelancaran Service Agreement System.</p>
    </div>

    @php
        $totalApprovals = $pendingApprovals->count();
        $totalLegalTasks = $legalTasks->count();
        $totalQcTasks = $qcTasks->count();
    @endphp

    @if($totalApprovals === 0 && $totalLegalTasks === 0 && $totalQcTasks === 0)
        <div class="empty-state card modern-card mt-4" style="padding: 40px;">
            <i class="fas fa-check-circle empty-icon text-success" style="font-size: 48px;"></i>
            <h4 style="margin-top: 16px; font-size: 18px; font-weight: 700;">Hore! Semua Tugas Selesai</h4>
            <p style="font-size: 14px; margin-top: 8px;">Anda tidak memiliki tugas persetujuan, review legal, maupun proses QC saat ini.</p>
        </div>
    @endif

    {{-- 1. PENDING APPROVALS --}}
    @if($totalApprovals > 0)
        <details class="card modern-card mb-4" open>
            <summary class="card-header" style="cursor: pointer; user-select: none; list-style: none;">
                <div class="card-title d-flex align-items-center gap-2" style="font-size: 16px;">
                    <i class="fas fa-check-double text-primary"></i> 
                    Persetujuan USPK (Approval)
                    <span class="badge badge-pending" style="margin-left: 8px;">{{ $totalApprovals }} Menunggu</span>
                </div>
                <i class="fas fa-chevron-down text-muted"></i>
            </summary>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 table-hover">
                        <thead>
                            <tr>
                                <th>No. USPK</th>
                                <th>Deskripsi</th>
                                <th>Pengaju</th>
                                <th>Tanggal</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingApprovals as $uspk)
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-primary);">{{ $uspk->uspk_number }}</div>
                                        <div style="font-size: 12px; color: var(--text-muted);">{{ $uspk->department->name ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;">{{ $uspk->title }}</div>
                                    </td>
                                    <td>{{ $uspk->submitter->name ?? '-' }}</td>
                                    <td>{{ $uspk->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('sas.uspk.show', $uspk->id) }}" class="btn btn-primary btn-sm">
                                            Proses <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </details>
    @endif

    {{-- 2. LEGAL TASKS --}}
    @if($totalLegalTasks > 0)
        <details class="card modern-card mb-4" open>
            <summary class="card-header" style="cursor: pointer; user-select: none; list-style: none;">
                <div class="card-title d-flex align-items-center gap-2" style="font-size: 16px;">
                    <i class="fas fa-balance-scale text-warning"></i> 
                    Review Legal SPK
                    <span class="badge badge-warning" style="margin-left: 8px; background: rgba(245, 158, 11, 0.1); color: #d97706;">{{ $totalLegalTasks }} Dokumen</span>
                </div>
                <i class="fas fa-chevron-down text-muted"></i>
            </summary>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 table-hover">
                        <thead>
                            <tr>
                                <th>No. USPK</th>
                                <th>Kontraktor Pemenang</th>
                                <th>Pengaju</th>
                                <th>Revisi Status</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($legalTasks as $uspk)
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-primary);">{{ $uspk->uspk_number }}</div>
                                        <div style="font-size: 12px; color: var(--text-muted);">{{ $uspk->department->name ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;">{{ $uspk->selectedTender->contractor->name ?? '-' }}</div>
                                        <div style="font-size: 12px; color: var(--text-muted);">Rp {{ number_format($uspk->selectedTender->tender_value ?? 0, 0, ',', '.') }}</div>
                                    </td>
                                    <td>{{ $uspk->submitter->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-approved">Approved Final</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('sas.uspk.show', $uspk->id) }}" class="btn btn-warning btn-sm" style="color:#000;">
                                            Proses <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </details>
    @endif

    {{-- 3. QC TASKS --}}
    @if($totalQcTasks > 0)
        <details class="card modern-card mb-4" open>
            <summary class="card-header" style="cursor: pointer; user-select: none; list-style: none;">
                <div class="card-title d-flex align-items-center gap-2" style="font-size: 16px;">
                    <i class="fas fa-clipboard-check" style="color: var(--info);"></i> 
                    Verifikasi QC Lapangan
                    <span class="badge badge-in_verification" style="margin-left: 8px;">{{ $totalQcTasks }} Tugas</span>
                </div>
                <i class="fas fa-chevron-down text-muted"></i>
            </summary>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 table-hover">
                        <thead>
                            <tr>
                                <th>No. USPK</th>
                                <th>Target Verifikasi</th>
                                <th>Status QC</th>
                                <th>Tanggal SPK</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($qcTasks as $uspk)
                                @php
                                    $qcStatusLabels = [
                                        \Modules\ServiceAgreementSystem\Models\UspkSubmission::QC_STATUS_PENDING_ASSIGNMENT => 'Tunggu Digassign',
                                        \Modules\ServiceAgreementSystem\Models\UspkSubmission::QC_STATUS_ASSIGNED => 'Tunggu Pekerjaan Selesai',
                                        \Modules\ServiceAgreementSystem\Models\UspkSubmission::QC_STATUS_IN_VERIFICATION => 'Perlu Verifikasi',
                                        \Modules\ServiceAgreementSystem\Models\UspkSubmission::QC_STATUS_REVISION_REQUIRED => 'Butuh Revisi (Rejected)',
                                    ];
                                @endphp
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-primary);">{{ $uspk->uspk_number }}</div>
                                        <div style="font-size: 12px; color: var(--text-muted);">{{ $uspk->department->name ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;">{{ $uspk->title }}</div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">
                                            {{ $qcStatusLabels[$uspk->qc_status] ?? $uspk->qc_status }}
                                        </span>
                                    </td>
                                    <td>{{ $uspk->submitter_signed_spk_uploaded_at?->format('d M Y') ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('sas.uspk.show', $uspk->id) }}" class="btn btn-info btn-sm" style="color:#fff; background:var(--info);">
                                            Proses <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </details>
    @endif

    @push('styles')
    <style>
        details summary::-webkit-details-marker { display: none; }
        details[open] summary {
            border-bottom: 1px solid var(--border-color);
        }
        details[open] summary .fa-chevron-down {
            transform: rotate(180deg);
        }
        details summary .fa-chevron-down {
            transition: transform 0.3s ease;
        }
    </style>
    @endpush

</x-serviceagreementsystem::layouts.master>
