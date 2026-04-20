<?php if (isset($component)) { $__componentOriginal8ffca6aaed06613cf5643e13e74c8806 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806 = $attributes; } ?>
<?php $component = Modules\ServiceAgreementSystem\View\Components\Layouts\Master::resolve(['title' => 'Pengajuan USPK'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('serviceagreementsystem::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Modules\ServiceAgreementSystem\View\Components\Layouts\Master::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    
    <?php $__env->startPush('styles'); ?>
    <style>
        /* Tambahan CSS ringan untuk mempercantik UI */
        .filter-tabs .btn {
            border-radius: 20px;
            padding: 0.4rem 1.2rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .table-custom th {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #6b7280;
            background-color: #f9fafb;
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .table-custom td {
            vertical-align: middle;
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
        }
        .table-custom tbody tr:hover {
            background-color: #f8fafc;
        }
        .btn-action {
            background: #f1f5f9;
            border: none;
            color: #64748b;
            transition: all 0.2s;
        }
        .btn-action:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .btn-action.delete:hover {
            background: #fee2e2;
            color: #dc2626;
        }
    </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('actions'); ?>
        <a href="<?php echo e(route('sas.uspk.create')); ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
            <i class="fas fa-plus me-1"></i> Buat USPK Baru
        </a>
    <?php $__env->stopPush(); ?>

    
    <div class="d-flex gap-2 flex-wrap mb-4 filter-tabs">
        <a href="<?php echo e(route('sas.uspk.index')); ?>" class="btn btn-sm <?php echo e(!$status ? 'btn-primary shadow-sm' : 'btn-light text-muted border'); ?>">Semua</a>
        <a href="<?php echo e(route('sas.uspk.index', ['status' => 'draft'])); ?>" class="btn btn-sm <?php echo e($status === 'draft' ? 'btn-primary shadow-sm' : 'btn-light text-muted border'); ?>">Draft</a>
        <a href="<?php echo e(route('sas.uspk.index', ['status' => 'submitted'])); ?>" class="btn btn-sm <?php echo e($status === 'submitted' ? 'btn-primary shadow-sm' : 'btn-light text-muted border'); ?>">Submitted</a>
        <a href="<?php echo e(route('sas.uspk.index', ['status' => 'in_review'])); ?>" class="btn btn-sm <?php echo e($status === 'in_review' ? 'btn-primary shadow-sm' : 'btn-light text-muted border'); ?>">In Review</a>
        <a href="<?php echo e(route('sas.uspk.index', ['status' => 'approved'])); ?>" class="btn btn-sm <?php echo e($status === 'approved' ? 'btn-primary shadow-sm' : 'btn-light text-muted border'); ?>">Approved</a>
        <a href="<?php echo e(route('sas.uspk.index', ['status' => 'rejected'])); ?>" class="btn btn-sm <?php echo e($status === 'rejected' ? 'btn-primary shadow-sm' : 'btn-light text-muted border'); ?>">Rejected</a>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="table-responsive">
            <table class="table table-custom mb-0 w-100">
                <thead>
                    <tr>
                        <th>No. USPK</th>
                        <th>Judul Pekerjaan</th>
                        <th>Department</th>
                        <th>Blok</th>
                        <th style="text-align: right;">Estimasi Nilai</th>
                        <th class="text-center">Tender</th>
                        <th class="text-center">Status</th>
                        <th>SPK Final</th>
                        <th>Tanggal</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uspk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $canDownloadFinalSpk = $uspk->hasFinalSpkDocument() && (
                            (int) ($uspk->submitted_by ?? 0) === (int) auth()->id()
                            || in_array(strtolower(trim((string) auth()->user()?->moduleRole('sas'))), ['legal', 'admin'], true)
                            || auth()->user()?->hasAnyRole(['Legal', 'Admin', 'Super Admin'])
                        );
                        $qcStatusLabels = [
                            'pending_assignment' => 'Menunggu Penugasan QC',
                            'assigned' => 'Menunggu Laporan Selesai',
                            'in_verification' => 'Verifikasi QC Berjalan',
                            'verified' => 'Terverifikasi QC',
                            'revision_required' => 'Perlu Revisi Pekerjaan',
                        ];
                    ?>
                    <tr>
                        <td style="font-weight: 600;">
                            <a href="<?php echo e(route('sas.uspk.show', $uspk)); ?>" style="color: var(--accent, #2563eb); text-decoration: none;">
                                <?php echo e($uspk->uspk_number); ?>

                            </a>
                        </td>
                        <td>
                            <span class="d-block text-truncate" style="max-width: 200px;" title="<?php echo e($uspk->title); ?>">
                                <?php echo e($uspk->title); ?>

                            </span>
                        </td>
                        <td><span class="text-muted"><?php echo e($uspk->department->name ?? '-'); ?></span></td>
                        <td title="<?php echo e($uspk->blocks->pluck('name')->join(', ')); ?>">
                            <div class="d-flex align-items-center">
                                <span class="text-muted text-truncate" style="max-width: 120px;">
                                    <?php echo e($uspk->blocks->first()->name ?? '-'); ?>

                                </span>
                                
                                <?php if($uspk->blocks && $uspk->blocks->count() > 1): ?>
                                    <span class="badge rounded-pill ms-1" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-size: 0.7rem; padding: 0.2rem 0.4rem; cursor: help;">
                                        +<?php echo e($uspk->blocks->count() - 1); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td style="text-align: right; font-family: monospace; font-size: 0.95rem; font-weight: 600;">
                            Rp <?php echo e(number_format($uspk->estimated_value, 0, ',', '.')); ?>

                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">
                                <?php echo e($uspk->tenders->count()); ?> Kontraktor
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-<?php echo e($uspk->status); ?> px-2 py-1 rounded-pill">
                                <?php echo e(ucfirst(str_replace('_', ' ', $uspk->status))); ?>

                            </span>
                        </td>
                        <td>
                            <?php if($uspk->hasFinalSpkDocument()): ?>
                                <div class="d-flex flex-column align-items-start gap-1">
                                    <span class="badge rounded-pill" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 0.35rem 0.6rem;">
                                        <i class="fas fa-check-circle me-1"></i> Sudah Dikirim
                                    </span>
                                    <small style="color: #6b7280; font-size: 11px;">
                                        <?php echo e(optional($uspk->legal_spk_uploaded_at)->format('d M Y H:i') ?? '-'); ?>

                                    </small>
                                    <?php if($canDownloadFinalSpk): ?>
                                        <a href="<?php echo e(route('sas.uspk-legal.download', $uspk)); ?>" class="text-decoration-none mt-1" style="font-size: 12px; font-weight: 600; color: #047857;">
                                            <i class="fas fa-download me-1"></i> SPK
                                        </a>
                                    <?php endif; ?>

                                    <?php if($uspk->hasSubmitterSignedSpkDocument()): ?>
                                        <span class="badge rounded-pill mt-1" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 0.3rem 0.55rem;">
                                            <i class="fas fa-signature me-1"></i> SPK TTD Pengaju
                                        </span>
                                    <?php endif; ?>

                                    <?php if($uspk->qc_status): ?>
                                        <small style="color: #475569; font-size: 11px; margin-top: 3px; display: block;">
                                            QC: <?php echo e($qcStatusLabels[$uspk->qc_status] ?? ucfirst(str_replace('_', ' ', $uspk->qc_status))); ?>

                                        </small>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="badge rounded-pill" style="background: #fff7ed; color: #9a3412; border: 1px solid #fed7aa; padding: 0.35rem 0.6rem;">
                                    <i class="fas fa-hourglass-half me-1"></i> Belum Dikirim
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="color: #4b5563; font-size: 0.9rem;">
                            <?php echo e($uspk->created_at->format('d M Y')); ?>

                        </td>
                        <td style="text-align: center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="<?php echo e(route('sas.uspk.show', $uspk)); ?>" class="btn btn-sm btn-action" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if($uspk->isEditable()): ?>
                                <a href="<?php echo e(route('sas.uspk.edit', $uspk)); ?>" class="btn btn-sm btn-action" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('sas.uspk.destroy', $uspk)); ?>" method="POST" onsubmit="return confirm('Yakin ingin menghapus USPK ini?')" class="d-inline">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-action delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div class="empty-state text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3" style="color: #cbd5e1;"></i>
                                <h6 class="mb-1 text-dark">Belum ada pengajuan USPK</h6>
                                <p class="small">Data pengajuan akan muncul di sini setelah dibuat.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if($submissions->hasPages()): ?>
        <div class="card-footer bg-white border-top-0 py-3">
            <?php echo e($submissions->links()); ?>

        </div>
        <?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ffca6aaed06613cf5643e13e74c8806)): ?>
<?php $attributes = $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806; ?>
<?php unset($__attributesOriginal8ffca6aaed06613cf5643e13e74c8806); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ffca6aaed06613cf5643e13e74c8806)): ?>
<?php $component = $__componentOriginal8ffca6aaed06613cf5643e13e74c8806; ?>
<?php unset($__componentOriginal8ffca6aaed06613cf5643e13e74c8806); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/ServiceAgreementSystem\resources/views/uspk/index.blade.php ENDPATH**/ ?>