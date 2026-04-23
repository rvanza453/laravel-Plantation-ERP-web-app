<x-serviceagreementsystem::layouts.master :title="'Daftar USPK'">

    @push('styles')
    <style>
        /* ===== Page Entry Animation ===== */
        .uspk-page { animation: pageIn 0.45s cubic-bezier(0.4,0,0.2,1); }
        @keyframes pageIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ===== Page Header ===== */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .page-header-left h1 {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin: 0 0 4px;
        }
        .page-header-left p {
            font-size: 13.5px;
            color: #64748b;
            margin: 0;
        }
        .btn-create {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            border-radius: 12px;
            padding: 11px 22px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            box-shadow: 0 4px 14px rgba(37,99,235,0.35);
            transition: all 0.25s ease;
        }
        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37,99,235,0.4);
            color: #fff;
        }

        /* ===== Reminder Cards ===== */
        .reminder-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }
        @media(max-width: 900px) { .reminder-section { grid-template-columns: 1fr; } }

        .reminder-box {
            border-radius: 16px;
            padding: 20px;
            border: 1px solid;
        }
        .reminder-box.deadline { background: #f0f7ff; border-color: #bfdbfe; }
        .reminder-box.approval { background: #fffdf0; border-color: #fde68a; }

        .reminder-box-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }
        .reminder-box-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }
        .reminder-box.deadline .reminder-box-icon { background: #dbeafe; color: #2563eb; }
        .reminder-box.approval .reminder-box-icon { background: #fef9c3; color: #b45309; }

        .reminder-box-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }
        .reminder-box-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .reminder-item-list { display: flex; flex-direction: column; gap: 8px; }
        .reminder-item {
            background: rgba(255,255,255,0.8);
            border: 1px solid rgba(226,232,240,0.7);
            border-radius: 10px;
            padding: 10px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }
        .reminder-item-label { font-size: 12.5px; font-weight: 700; color: #0f172a; }
        .reminder-item-meta { font-size: 11.5px; color: #475569; margin-top: 2px; }
        .overdue-text { color: #dc2626; font-weight: 700; }
        .reminder-open-btn {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.2s;
        }
        .reminder-open-btn:hover { background: #f1f5f9; color: #0f172a; }
        .reminder-empty { font-size: 12.5px; color: #94a3b8; padding: 8px 0; }

        /* ===== Filter Tabs ===== */
        .filter-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .filter-tab {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: 1.5px solid transparent;
            transition: all 0.2s ease;
            color: #64748b;
            background: #f1f5f9;
        }
        .filter-tab:hover { background: #e2e8f0; color: #0f172a; }
        .filter-tab.active { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
        .filter-tab .tab-count {
            background: rgba(100,116,139,0.15);
            border-radius: 10px;
            padding: 1px 7px;
            font-size: 11px;
        }
        .filter-tab.active .tab-count { background: rgba(37,99,235,0.12); }

        /* ===== Table Card ===== */
        .table-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid rgba(226,232,240,0.9);
            box-shadow: 0 4px 20px rgba(15,23,42,0.06);
            overflow: hidden;
        }
        .table-card-header {
            padding: 18px 28px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fafafa;
        }
        .table-card-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .table-card-title i {
            color: #3b82f6;
        }
        .total-badge {
            background: #eff6ff;
            color: #2563eb;
            border-radius: 8px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        /* ===== Data Table ===== */
        .uspk-table { width: 100%; border-collapse: collapse; }
        .uspk-table thead th {
            padding: 14px 20px;
            text-align: left;
            font-size: 11.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }
        .uspk-table tbody td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 13.5px;
            color: #334155;
        }
        .uspk-table tbody tr:last-child td { border-bottom: none; }
        .uspk-table tbody tr { transition: background 0.18s; }
        .uspk-table tbody tr:hover td { background: #f8fafc; }

        /* USPK Number cell */
        .uspk-num-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: #0f172a;
            text-decoration: none;
            transition: color 0.2s;
        }
        .uspk-num-link:hover { color: #2563eb; }
        .uspk-num-link i { color: #94a3b8; font-size: 12px; }

        /* Title cell */
        .uspk-title {
            font-weight: 500;
            color: #1e293b;
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        /* Department cell */
        .dept-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12.5px;
            color: #475569;
        }
        .dept-pill i { color: #94a3b8; font-size: 11px; }

        /* Block cell */
        .block-cell { display: flex; align-items: center; gap: 6px; }
        .block-plus {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 1px 7px;
            font-size: 11px;
            font-weight: 700;
        }

        /* Value cell */
        .value-cell {
            text-align: right;
            font-family: 'Inter', monospace;
            font-weight: 700;
            font-size: 13px;
            color: #0f172a;
            white-space: nowrap;
        }

        /* Tender cell */
        .tender-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 4px 10px;
            font-size: 12px;
            color: #475569;
        }
        .tender-pill i { color: #94a3b8; }

        /* Status badge */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-pill::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .sp-draft         { background: #f1f5f9; color: #475569; }
        .sp-draft::before { background: #94a3b8; }
        .sp-submitted         { background: #fffbeb; color: #b45309; }
        .sp-submitted::before { background: #f59e0b; }
        .sp-in_review         { background: #f5f3ff; color: #6d28d9; }
        .sp-in_review::before { background: #8b5cf6; }
        .sp-approved         { background: #ecfdf5; color: #065f46; }
        .sp-approved::before { background: #10b981; }
        .sp-rejected         { background: #fef2f2; color: #b91c1c; }
        .sp-rejected::before { background: #ef4444; }

        /* SPK Legal column */
        .spk-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .spk-tag-ready { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .spk-tag-pending { background: #fff7ed; color: #9a3412; border: 1px solid #fed7aa; }
        .spk-tag-signed { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .spk-tag-unsigned { background: #fffbeb; color: #92400e; border: 1px solid #fcd34d; }
        .spk-download {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 600;
            color: #047857;
            text-decoration: none;
        }
        .spk-download:hover { text-decoration: underline; }

        /* Action buttons */
        .action-group { display: flex; gap: 6px; justify-content: center; }
        .act-btn {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }
        .act-btn:hover { background: #e2e8f0; color: #0f172a; border-color: #cbd5e1; }
        .act-btn.act-edit:hover { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
        .act-btn.act-delete:hover { background: #fef2f2; color: #dc2626; border-color: #fecaca; }

        /* Submitter avatar */
        .submitter-cell { display: flex; align-items: center; gap: 8px; }
        .submitter-avatar {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
            color: #4338ca;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 800;
            flex-shrink: 0;
        }
        .submitter-name { font-size: 13px; color: #334155; }

        /* Date cell */
        .date-cell { color: #64748b; font-size: 13px; white-space: nowrap; }

        /* Empty State */
        .empty-row td { border: none !important; }
        .empty-state-box {
            padding: 72px 20px;
            text-align: center;
        }
        .empty-state-icon {
            width: 72px;
            height: 72px;
            background: #f1f5f9;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #94a3b8;
            margin-bottom: 20px;
        }
        .empty-state-box h4 { font-size: 17px; font-weight: 700; color: #0f172a; margin: 0 0 8px; }
        .empty-state-box p { font-size: 14px; color: #64748b; margin: 0; }

        /* Pagination */
        .pagination-area {
            padding: 16px 28px;
            border-top: 1px solid #f1f5f9;
            background: #fafafa;
        }

        /* Alert banner */
        .alert-banner {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-left: 4px solid;
            border-radius: 10px;
            padding: 12px 18px;
            margin-bottom: 16px;
            font-size: 13.5px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .alert-banner.success { border-left-color: #10b981; background: #ecfdf5; color: #065f46; }
        .alert-banner.error   { border-left-color: #ef4444; background: #fef2f2; color: #b91c1c; }

        /* QC status small label */
        .qc-label { font-size: 11px; color: #64748b; margin-top: 4px; display: block; }
    </style>
    @endpush

    <div class="uspk-page">

        {{-- Alerts --}}
        @if(session('success'))
        <div class="alert-banner success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert-banner error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif

        {{-- Page Header --}}
        <div class="page-header">
            <div class="page-header-left">
                <h1>Pengajuan USPK</h1>
                <p>Kelola seluruh usulan surat perintah kerja di sini.</p>
            </div>
            @can('sas.role:Staff,Admin')
            <a href="{{ route('sas.uspk.create') }}" class="btn-create">
                <i class="fas fa-plus"></i> Buat USPK Baru
            </a>
            @else
            {{-- Fallback: always show for Staff & Admin based on session role --}}
            @if(in_array(auth()->user()?->moduleRole('sas'), ['Staff', 'Admin', 'staff', 'admin']))
            <a href="{{ route('sas.uspk.create') }}" class="btn-create">
                <i class="fas fa-plus"></i> Buat USPK Baru
            </a>
            @endif
            @endcan
        </div>

        {{-- Reminder Section --}}
        <div class="reminder-section">
            {{-- Deadline Reminder --}}
            <div class="reminder-box deadline">
                <div class="reminder-box-header">
                    <div class="reminder-box-icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="reminder-box-title">Tenggat Waktu Blok</div>
                        <div class="reminder-box-subtitle">
                            {{ $reminders['canManageDeadline'] ? 'Atur deadline di detail USPK.' : 'Pantau status tenggat blok Anda.' }}
                        </div>
                    </div>
                </div>
                <div class="reminder-item-list">
                    @forelse($reminders['deadlineAlertItems'] as $deadlineReminder)
                    @php
                        $item = $deadlineReminder['submission'];
                        $nearestDeadline = $deadlineReminder['nearest_deadline'];
                        $overdueCount = (int) $deadlineReminder['overdue_count'];
                        $dueSoonCount = (int) $deadlineReminder['due_soon_count'];
                    @endphp
                    <div class="reminder-item">
                        <div>
                            <div class="reminder-item-label">{{ $item->uspk_number }}</div>
                            <div class="reminder-item-meta">
                                @if($overdueCount > 0)
                                    <span class="overdue-text"><i class="fas fa-exclamation-triangle"></i> {{ $overdueCount }} blok overdue</span>
                                @else
                                    {{ $dueSoonCount }} blok deadline ≤ 3 hari
                                @endif
                                @if($nearestDeadline)
                                    · Terdekat: <strong>{{ $nearestDeadline->format('d M Y') }}</strong>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('sas.uspk.show', $item) }}" class="reminder-open-btn">Buka</a>
                    </div>
                    @empty
                    <div class="reminder-empty"><i class="fas fa-check-circle" style="color:#10b981;"></i> Tidak ada deadline mendesak saat ini.</div>
                    @endforelse

                    @if($reminders['deadlineSetupItems']->isNotEmpty())
                    <div class="reminder-item" style="background: #fff7ed; border-color: #fde68a;">
                        <div>
                            <div class="reminder-item-label" style="color: #b45309;">Deadline Belum Diatur</div>
                            <div class="reminder-item-meta">{{ $reminders['deadlineSetupItems']->count() }} USPK memiliki blok tanpa deadline.</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Approval & Legal Reminder --}}
            <div class="reminder-box approval">
                <div class="reminder-box-header">
                    <div class="reminder-box-icon"><i class="fas fa-tasks"></i></div>
                    <div>
                        <div class="reminder-box-title">Approval & Legal</div>
                        <div class="reminder-box-subtitle">Item yang memerlukan tindakan segera.</div>
                    </div>
                </div>
                <div class="reminder-item-list">
                    <div class="reminder-item">
                        <div>
                            <div class="reminder-item-label">Approval Menunggu Aksi</div>
                            <div class="reminder-item-meta">
                                @if($reminders['approvalActionItems']->count() > 0)
                                    <span style="color:#b45309; font-weight:700;">{{ $reminders['approvalActionItems']->count() }} USPK</span> perlu diproses
                                @else
                                    Tidak ada approval pending
                                @endif
                            </div>
                        </div>
                        @if($reminders['approvalActionItems']->isNotEmpty())
                        <a href="{{ route('sas.uspk.index', ['status' => 'in_review']) }}" class="reminder-open-btn">Lihat</a>
                        @endif
                    </div>
                    <div class="reminder-item">
                        <div>
                            <div class="reminder-item-label">SPK Final Legal Belum Terbit</div>
                            <div class="reminder-item-meta">
                                @if($reminders['legalPublishItems']->count() > 0)
                                    <span style="color:#b45309; font-weight:700;">{{ $reminders['legalPublishItems']->count() }} USPK</span> menunggu dokumen legal
                                @else
                                    Semua SPK sudah dikirim Legal
                                @endif
                            </div>
                        </div>
                        @if($reminders['legalPublishItems']->isNotEmpty())
                        <a href="{{ route('sas.uspk-legal.index') }}" class="reminder-open-btn">Lihat</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="filter-bar">
            <a href="{{ route('sas.uspk.index') }}"
               class="filter-tab {{ !$status ? 'active' : '' }}">
                <i class="fas fa-list"></i> Semua
            </a>
            <a href="{{ route('sas.uspk.index', ['status' => 'draft']) }}"
               class="filter-tab {{ $status === 'draft' ? 'active' : '' }}">
                <i class="fas fa-pencil-alt"></i> Draft
            </a>
            <a href="{{ route('sas.uspk.index', ['status' => 'submitted']) }}"
               class="filter-tab {{ $status === 'submitted' ? 'active' : '' }}">
                <i class="fas fa-paper-plane"></i> Submitted
            </a>
            <a href="{{ route('sas.uspk.index', ['status' => 'in_review']) }}"
               class="filter-tab {{ $status === 'in_review' ? 'active' : '' }}">
                <i class="fas fa-search"></i> In Review
            </a>
            <a href="{{ route('sas.uspk.index', ['status' => 'approved']) }}"
               class="filter-tab {{ $status === 'approved' ? 'active' : '' }}">
                <i class="fas fa-check-circle"></i> Approved
            </a>
            <a href="{{ route('sas.uspk.index', ['status' => 'rejected']) }}"
               class="filter-tab {{ $status === 'rejected' ? 'active' : '' }}">
                <i class="fas fa-times-circle"></i> Rejected
            </a>
        </div>

        {{-- Table Card --}}
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-title">
                    <i class="fas fa-file-contract"></i> Daftar USPK
                </div>
                <span class="total-badge">{{ $submissions->total() }} Total</span>
            </div>
            <div style="overflow-x: auto;">
                <table class="uspk-table">
                    <thead>
                        <tr>
                            <th>No. USPK</th>
                            <th>Nama Kegiatan</th>
                            <th>Department / Blok</th>
                            <th>Pengaju</th>
                            <th class="text-right" style="text-align:right">Nilai Pekerjaan</th>
                            <th style="text-align:center">Kontraktor</th>
                            <th style="text-align:center">Status</th>
                            <th>Dokumen SPK</th>
                            <th>Dibuat</th>
                            <th style="text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $uspk)
                        @php
                            $canDownloadFinal = $uspk->hasFinalSpkDocument() && (
                                (int)($uspk->submitted_by ?? 0) === (int)auth()->id()
                                || in_array(strtolower(trim((string) auth()->user()?->moduleRole('sas'))), ['legal', 'admin'], true)
                                || auth()->user()?->hasAnyRole(['Legal', 'Admin', 'Super Admin'])
                            );
                            $qcLabels = [
                                'pending_assignment' => 'Menunggu Penugasan QC',
                                'assigned'           => 'Menunggu Laporan Selesai',
                                'in_verification'    => 'Verifikasi QC Berjalan',
                                'verified'           => 'Terverifikasi QC',
                                'revision_required'  => 'Perlu Revisi',
                            ];
                        @endphp
                        <tr>
                            {{-- No. USPK --}}
                            <td>
                                <a href="{{ route('sas.uspk.show', $uspk) }}" class="uspk-num-link">
                                    <i class="fas fa-hashtag"></i>
                                    {{ $uspk->uspk_number }}
                                </a>
                            </td>

                            {{-- Nama Kegiatan --}}
                            <td title="{{ $uspk->title }}">
                                <span class="uspk-title">{{ $uspk->title }}</span>
                            </td>

                            {{-- Department & Blok (merged into one practical cell) --}}
                            <td>
                                <div class="dept-pill" style="margin-bottom:5px;">
                                    <i class="fas fa-building"></i>
                                    {{ $uspk->department->name ?? '-' }}
                                </div>
                                <div class="block-cell">
                                    <span style="font-size:12px;color:#475569;">
                                        <i class="fas fa-map-marker-alt" style="color:#94a3b8;margin-right:4px;"></i>
                                        {{ $uspk->blocks->first()->name ?? '-' }}
                                    </span>
                                    @if($uspk->blocks && $uspk->blocks->count() > 1)
                                    <span class="block-plus" title="{{ $uspk->blocks->pluck('name')->join(', ') }}">
                                        +{{ $uspk->blocks->count() - 1 }}
                                    </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Pengaju --}}
                            <td>
                                <div class="submitter-cell">
                                    <div class="submitter-avatar">
                                        {{ substr($uspk->submitter->name ?? '?', 0, 2) }}
                                    </div>
                                    <span class="submitter-name">{{ $uspk->submitter->name ?? '-' }}</span>
                                </div>
                            </td>

                            {{-- Nilai Estimasi --}}
                            <td class="value-cell">
                                @if((float) $uspk->estimated_value > 0)
                                    Rp&nbsp;{{ number_format($uspk->estimated_value, 0, ',', '.') }}
                                @else
                                    <span style="color:#94a3b8;">-</span>
                                @endif
                            </td>

                            {{-- Kontraktor / Tender --}}
                            <td style="text-align:center;">
                                <span class="tender-pill">
                                    <i class="fas fa-users"></i>
                                    {{ $uspk->tenders->count() }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td style="text-align:center;">
                                <span class="status-pill sp-{{ strtolower($uspk->status) }}">
                                    {{ ucfirst(str_replace('_', ' ', $uspk->status)) }}
                                </span>
                            </td>

                            {{-- Dokumen SPK --}}
                            <td>
                                @if($uspk->hasFinalSpkDocument())
                                    <span class="spk-tag spk-tag-ready">
                                        <i class="fas fa-check-circle"></i> SPK Final
                                    </span><br>

                                    @if($canDownloadFinal)
                                    <a href="{{ route('sas.uspk-legal.download', $uspk) }}" class="spk-download">
                                        <i class="fas fa-download"></i> Unduh SPK
                                    </a><br>
                                    @endif

                                    @if($uspk->hasSubmitterSignedSpkDocument())
                                        <span class="spk-tag spk-tag-signed" style="margin-top:4px;">
                                            <i class="fas fa-signature"></i> TTD: Upload
                                        </span>
                                    @else
                                        <span class="spk-tag spk-tag-unsigned" style="margin-top:4px;">
                                            <i class="fas fa-signature"></i> TTD: Belum
                                        </span>
                                    @endif

                                    @if($uspk->qc_status)
                                        <span class="qc-label">QC: {{ $qcLabels[$uspk->qc_status] ?? ucfirst(str_replace('_', ' ', $uspk->qc_status)) }}</span>
                                    @endif
                                @else
                                    <span class="spk-tag spk-tag-pending">
                                        <i class="fas fa-hourglass-half"></i> Belum Dikirim
                                    </span>
                                @endif
                            </td>

                            {{-- Tanggal --}}
                            <td class="date-cell">
                                <i class="fas fa-calendar-alt" style="color:#94a3b8;margin-right:4px;"></i>
                                {{ $uspk->created_at->format('d M Y') }}
                            </td>

                            {{-- Aksi --}}
                            <td>
                                <div class="action-group">
                                    <a href="{{ route('sas.uspk.show', $uspk) }}" class="act-btn" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($uspk->isEditable())
                                    <a href="{{ route('sas.uspk.edit', $uspk) }}" class="act-btn act-edit" title="Edit USPK">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('sas.uspk.destroy', $uspk) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus USPK {{ $uspk->uspk_number }}?')"
                                          style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="act-btn act-delete" title="Hapus USPK">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="empty-row">
                            <td colspan="10">
                                <div class="empty-state-box">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-folder-open"></i>
                                    </div>
                                    <h4>Belum Ada Pengajuan USPK</h4>
                                    <p>{{ $status ? "Tidak ada USPK dengan status \"".ucfirst(str_replace('_',' ',$status))."\"." : 'Data pengajuan USPK akan muncul di sini setelah dibuat.' }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($submissions->hasPages())
            <div class="pagination-area">
                {{ $submissions->links() }}
            </div>
            @endif
        </div>

    </div>

</x-serviceagreementsystem::layouts.master>