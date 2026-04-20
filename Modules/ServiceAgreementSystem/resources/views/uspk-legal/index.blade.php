<x-serviceagreementsystem::layouts.master :title="'Review Legal SPK'">
    
    <style>
        /* Custom Styles untuk Card Layout */
        .page-header {
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--border-color, #f3f4f6);
            padding-bottom: 1rem;
        }
        
        .uspk-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .uspk-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border-color, #e5e7eb);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .uspk-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-color: var(--accent, #3b82f6);
        }

        .uspk-card-header {
            padding: 1.25rem 1.25rem 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px dashed #e5e7eb;
        }

        .uspk-number {
            font-weight: 700;
            color: var(--accent, #3b82f6);
            font-size: 0.9rem;
            background: rgba(59, 130, 246, 0.1);
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            text-decoration: none;
        }

        .uspk-date {
            font-size: 0.8rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .uspk-card-body {
            padding: 1.25rem;
            flex-grow: 1;
        }

        .uspk-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary, #111827);
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            font-size: 0.85rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .info-label {
            color: #6b7280;
            text-transform: uppercase;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.05em;
        }

        .info-value {
            color: var(--text-primary, #374151);
            font-weight: 500;
        }

        .winner-highlight {
            margin-top: 1rem;
            padding: 0.75rem;
            background: #f8fafc;
            border-radius: 8px;
            border-left: 3px solid var(--success, #10b981);
        }

        .uspk-card-footer {
            padding: 1rem 1.25rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        .empty-state-card {
            grid-column: 1 / -1;
            padding: 4rem 2rem;
            text-align: center;
            background: #ffffff;
            border-radius: 12px;
            border: 2px dashed #e5e7eb;
        }
        
        .empty-icon {
            font-size: 3rem;
            color: var(--success, #10b981);
            margin-bottom: 1rem;
            opacity: 0.8;
        }
    </style>

    <div class="page-header d-flex justify-between align-center">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: var(--text-primary); margin-bottom: 0.25rem;">Review Legal SPK</h1>
            <p class="text-muted" style="font-size: 14px;">Daftar USPK yang sudah <strong>approved final</strong> dan menunggu dokumen SPK final dari Legal.</p>
        </div>
    </div>

    <div class="uspk-grid">
        @forelse($submissions as $uspk)
            <div class="uspk-card">
                <div class="uspk-card-header">
                    <a href="{{ route('sas.uspk.show', $uspk) }}" class="uspk-number">
                        <i class="fas fa-file-contract mr-1"></i> {{ $uspk->uspk_number }}
                    </a>
                    <div class="uspk-date" title="Tanggal Approved">
                        <i class="fas fa-clock"></i> 
                        {{ optional($uspk->updated_at)->format('d M Y, H:i') }}
                    </div>
                </div>

                <div class="uspk-card-body">
                    <h3 class="uspk-title">{{ Str::limit($uspk->title, 55) }}</h3>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Department</span>
                            <span class="info-value">
                                <i class="fas fa-building text-muted mr-1"></i> {{ $uspk->department->name ?? '-' }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Pengaju</span>
                            <span class="info-value">
                                <i class="fas fa-user text-muted mr-1"></i> {{ $uspk->submitter->name ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="winner-highlight">
                        <div class="info-item">
                            <span class="info-label text-success"><i class="fas fa-trophy mr-1"></i> Pemenang Final</span>
                            <span class="info-value mt-1">
                                @if($uspk->selectedTender)
                                    <strong>{{ $uspk->selectedTender->contractor->name ?? '-' }}</strong>
                                @else
                                    <span class="text-muted" style="font-style: italic;">Belum ada pemenang</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="uspk-card-footer">
                    <a href="{{ route('sas.uspk.show', $uspk) }}" class="btn btn-outline-secondary btn-sm" style="border-radius: 6px;">
                        <i class="fas fa-eye"></i> Detail
                    </a>
                    <a href="{{ route('sas.uspk-legal.export', $uspk) }}" class="btn btn-primary btn-sm" style="border-radius: 6px; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);">
                        <i class="fas fa-file-export"></i> Export Draft
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state-card">
                <i class="fas fa-check-circle empty-icon"></i>
                <h3 style="font-weight: 700; font-size: 1.25rem; color: var(--text-primary); margin-bottom: 0.5rem;">Tidak Ada Antrean Legal</h3>
                <p class="text-muted">Yeay! Semua USPK approved saat ini sudah memiliki dokumen SPK final.</p>
            </div>
        @endforelse
    </div>

    @if($submissions->hasPages())
        <div class="pagination-wrapper mt-4 d-flex justify-content-center">
            {{ $submissions->links() }}
        </div>
    @endif

</x-serviceagreementsystem::layouts.master>