<x-serviceagreementsystem::layouts.master :title="'Inbox QC USPK'">
    <div class="card mb-4">
        <div class="card-body">
            <h3 style="margin-bottom: 8px;">Inbox QC</h3>
            @if($isQcCoordinator)
                <p class="text-muted" style="margin: 0;">Daftar USPK yang sudah upload SPK TTD dan masuk pipeline QC. Anda dapat membuka detail untuk assign verifier atau monitoring verifikasi.</p>
            @else
                <p class="text-muted" style="margin: 0;">Daftar USPK yang menugaskan Anda sebagai verifier QC.</p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No. USPK</th>
                        <th>Judul</th>
                        <th>Pengaju</th>
                        <th>Dept</th>
                        <th>Status QC</th>
                        <th>Verifier</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $uspk)
                        @php
                            $statusLabelMap = [
                                'pending_assignment' => 'Menunggu Penugasan',
                                'assigned' => 'Menunggu Laporan Selesai',
                                'in_verification' => 'Sedang Diverifikasi',
                                'verified' => 'Terverifikasi',
                                'revision_required' => 'Perlu Revisi',
                            ];
                        @endphp
                        <tr>
                            <td>{{ $uspk->uspk_number }}</td>
                            <td>{{ $uspk->title }}</td>
                            <td>{{ $uspk->submitter->name ?? '-' }}</td>
                            <td>{{ $uspk->department->name ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $uspk->qc_status ?: 'draft' }}">
                                    {{ $statusLabelMap[$uspk->qc_status] ?? ucfirst(str_replace('_', ' ', (string) $uspk->qc_status)) }}
                                </span>
                            </td>
                            <td>
                                @if($uspk->qcVerifications->count() > 0)
                                    {{ $uspk->qcVerifications->pluck('verifier.name')->filter()->join(', ') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('sas.uspk.show', $uspk) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Buka Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Belum ada data QC untuk ditampilkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
            <div class="card-body" style="border-top: 1px solid var(--border-color);">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
</x-serviceagreementsystem::layouts.master>
