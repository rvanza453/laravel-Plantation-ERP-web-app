<x-systemispo::layouts.hr-master title="HR Dashboard">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    /* Mengubah font utama ke Inter (paling mendekati referensi desain PayFlow) */
    .payflow-dashboard {
        font-family: 'Inter', sans-serif;
        max-width: 1200px;
        margin: 0 auto;
        /* Simulasi background abu-abu hangat seperti di gambar */
        background-color: #f3f3f2;
        padding: 24px;
        border-radius: 32px;
        color: #111827;
    }

    /* Utilitas */
    .text-muted { color: #6b7280; font-size: 13px; font-weight: 500; }
    .title-md { font-size: 18px; font-weight: 600; letter-spacing: -0.02em; color: #111; margin-bottom: 4px; }
    
    /* Layout Grid */
    .pf-grid-top {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    @media (min-width: 992px) { .pf-grid-top { grid-template-columns: 350px 1fr; } }

    .pf-grid-modules {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    @media (min-width: 768px) { .pf-grid-modules { grid-template-columns: 1fr 1fr; } }

    /* --- KARTU GELAP (Dark Card - spt Total Balance) --- */
    .pf-card-dark {
        background: #232220;
        color: #ffffff;
        border-radius: 28px;
        padding: 28px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .pf-card-dark .header-flex { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; }
    .pf-card-dark .balance-label { color: #a1a1aa; font-size: 13px; margin-bottom: 8px; }
    .pf-card-dark .balance-value { font-size: 36px; font-weight: 600; letter-spacing: -0.03em; line-height: 1; }
    
    /* --- KARTU TERANG (Light Card) --- */
    .pf-card-light {
        background: #ffffff;
        border-radius: 28px;
        padding: 28px;
        border: 1px solid #e5e7eb;
    }

    /* Stats Item (di dalam Light Card) */
    .pf-stats-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        align-items: center;
        height: 100%;
    }
    .pf-stat-item { padding: 12px 0; }
    .pf-stat-value { font-size: 28px; font-weight: 600; color: #111; letter-spacing: -0.02em; margin-bottom: 4px; }
    .pf-stat-label { font-size: 13px; color: #6b7280; font-weight: 500; }

    /* --- TOMBOL (Buttons) --- */
    .pf-btn-group { display: flex; gap: 12px; margin-top: 24px; }
    .pf-btn {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 100px; /* Bentuk oval/pill penuh */
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .pf-btn-light { background: #ffffff; color: #111; border: 1px solid #e5e7eb; }
    .pf-btn-light:hover { background: #f9fafb; }
    
    .pf-btn-dark { background: #232220; color: #ffffff; border: 1px solid #232220; }
    .pf-btn-dark:hover { background: #333; }

    /* Module Card Custom */
    .module-content { margin-bottom: 24px; }
    .module-content p { font-size: 14px; color: #6b7280; line-height: 1.5; margin-top: 8px; }
    .pf-icon-circle {
        width: 48px; height: 48px; border-radius: 50%;
        background: #f3f4f6; color: #111;
        display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 16px;
    }

    /* --- TABEL MINIMALIS --- */
    .pf-table-wrapper { width: 100%; overflow-x: auto; margin-top: 16px; }
    .pf-table { width: 100%; border-collapse: collapse; text-align: left; }
    .pf-table th {
        font-size: 13px; font-weight: 500; color: #9ca3af;
        padding: 16px 8px 16px 0; border-bottom: 1px solid #e5e7eb;
    }
    .pf-table td {
        padding: 16px 8px 16px 0; border-bottom: 1px solid #f3f4f6;
        font-size: 14px; font-weight: 500; color: #111;
    }
    .pf-table tr:last-child td { border-bottom: none; }
    
    /* Status Pill dengan Dot (Khas referensi gambar) */
    .pf-status-pill {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 6px 14px; border-radius: 100px;
        background: #ffffff; border: 1px solid #e5e7eb;
        font-size: 12px; font-weight: 500; color: #111;
        text-transform: capitalize;
    }
    .pf-status-pill::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
    .dot-pending::before { background-color: #f59e0b; }
    .dot-processing::before { background-color: #3b82f6; }
    .dot-finished::before { background-color: #10b981; }
    .dot-cancelled::before { background-color: #ef4444; }

    /* Avatar Dummy */
    .pf-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        background: #e5e7eb; display: inline-flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 600; color: #4b5563; margin-right: 12px;
    }
</style>

<div class="payflow-dashboard">
    
    {{-- Baris Atas: Summary & Stats --}}
    <div class="pf-grid-top">
        
        {{-- Dark Card (Mirip Total Balance) --}}
        <div class="pf-card-dark">
            <div class="header-flex">
                <div>
                    <div class="title-md" style="color: #fff;">Sistem HR</div>
                    <div class="text-muted">Halo, {{ explode(' ', auth()->user()->name)[0] }}</div>
                </div>
                <div class="pf-btn-light" style="padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; border-color: transparent; background: rgba(255,255,255,0.1); color: #fff;">
                    <i class="fas fa-chart-pie mr-1"></i> Summary
                </div>
            </div>
            
            <div>
                <div class="balance-label">Total Tiket Dikelola</div>
                <div class="balance-value">{{ $stats['total'] ?? 0 }} <span style="font-size: 18px; color: #a1a1aa; font-weight: 500;">Data</span></div>
            </div>
        </div>

        {{-- Light Card: Rincian Status (Mirip Total Income/Expenses) --}}
        <div class="pf-card-light">
            <div class="title-md">Status Permintaan</div>
            <div class="text-muted mb-4">Ringkasan aktivitas tiket yang sedang berjalan</div>
            
            <div class="pf-stats-container">
                <div class="pf-stat-item border-r border-gray-100 pr-4">
                    <div class="pf-stat-value">{{ $stats['pending'] ?? 0 }}</div>
                    <div class="pf-stat-label"><span style="color:#f59e0b;">●</span> Menunggu</div>
                </div>
                <div class="pf-stat-item border-r border-gray-100 px-4">
                    <div class="pf-stat-value">{{ $stats['processing'] ?? 0 }}</div>
                    <div class="pf-stat-label"><span style="color:#3b82f6;">●</span> Diproses</div>
                </div>
                <div class="pf-stat-item pl-4">
                    <div class="pf-stat-value">{{ $stats['finished'] ?? 0 }}</div>
                    <div class="pf-stat-label"><span style="color:#10b981;">●</span> Selesai</div>
                </div>
            </div>
        </div>

    </div>

    {{-- Baris Tengah: Modul (Mirip Recent Contacts / Exchange) --}}
    <div class="pf-grid-modules">
        
        <div class="pf-card-light">
            <div class="pf-icon-circle"><i class="fas fa-folder-open"></i></div>
            <div class="module-content">
                <div class="title-md">Data Eksternal</div>
                <p>Kelola aliran data pihak eksternal, validasi dokumen, dan pantau tautan berbagi dengan aman.</p>
            </div>
            <div class="pf-btn-group">
                <a href="{{ route('hr.external-requests.index') }}" class="pf-btn pf-btn-dark">
                    Kelola Data
                </a>
            </div>
        </div>

        <div class="pf-card-light">
            <div class="pf-icon-circle"><i class="fas fa-shield-alt"></i></div>
            <div class="module-content">
                <div class="title-md">Audit ISPO</div>
                <p>Pantau status pemenuhan kriteria ISPO estate, kelola sertifikasi, dan unggah dokumen.</p>
            </div>
            <div class="pf-btn-group">
                <a href="{{ route('ispo.index') }}" class="pf-btn pf-btn-light">
                    Lihat Audit
                </a>
            </div>
        </div>

    </div>

    {{-- Baris Bawah: Tabel Transaksi (Mirip "Transactions") --}}
    <div class="pf-card-light">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div class="title-md">Aktivitas Terkini</div>
                <div class="text-muted">Riwayat permintaan data terbaru</div>
            </div>
            <div style="display: flex; gap: 8px;">
                <button class="pf-btn-light" style="width: 36px; height: 36px; padding: 0; border-radius: 50%;"><i class="fas fa-search"></i></button>
                <button class="pf-btn-light" style="width: 36px; height: 36px; padding: 0; border-radius: 50%;"><i class="fas fa-filter"></i></button>
            </div>
        </div>

        <div class="pf-table-wrapper">
            <table class="pf-table">
                <thead>
                    <tr>
                        <th>Pihak Peminta</th>
                        <th>Kategori Data</th>
                        <th>Tanggal Masuk</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRequests ?? [] as $ticket)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="pf-avatar">{{ strtoupper(substr(str_replace('_', ' ', $ticket->pihak_peminta), 0, 1)) }}</div>
                                    <div>
                                        <div style="color: #111;">{{ ucwords(str_replace('_', ' ', $ticket->pihak_peminta)) }}</div>
                                        <div class="text-muted" style="font-size: 12px;">#{{ $ticket->nomor_referensi }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="color: #4b5563;">{{ ucwords(str_replace('_', ' ', $ticket->kategori_data)) }}</td>
                            <td class="text-muted">{{ optional($ticket->tanggal_surat_masuk)->format('d M Y, H:i') ?: '-' }}</td>
                            <td>
                                @php
                                    $dotClass = match($ticket->status_proses) {
                                        'menunggu' => 'dot-pending',
                                        'sedang_diproses' => 'dot-processing',
                                        'menunggu_persetujuan_manajer' => 'dot-processing',
                                        'selesai' => 'dot-finished',
                                        'ditolak' => 'dot-cancelled',
                                        default => ''
                                    };
                                @endphp
                                <span class="pf-status-pill {{ $dotClass }}">{{ $ticket->status_proses }}</span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('hr.external-requests.show', $ticket) }}" class="text-muted" style="text-decoration: none; padding: 8px;">
                                    <i class="fas fa-ellipsis-h"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px 0; color: #9ca3af;">
                                Belum ada aktivitas terkini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(count($recentRequests ?? []) > 0)
        <div style="text-align: center; margin-top: 24px;">
            <a href="{{ route('hr.external-requests.index') }}" class="pf-btn pf-btn-light" style="padding: 10px 24px;">
                Lihat semua aktivitas
            </a>
        </div>
        @endif
    </div>

</div>
</x-systemispo::layouts.hr-master>