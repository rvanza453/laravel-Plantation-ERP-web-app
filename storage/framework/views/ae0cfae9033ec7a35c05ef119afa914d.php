<?php if (isset($component)) { $__componentOriginal8ffca6aaed06613cf5643e13e74c8806 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806 = $attributes; } ?>
<?php $component = Modules\ServiceAgreementSystem\View\Components\Layouts\Master::resolve(['title' => 'Kotak Tugas & Persetujuan'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('serviceagreementsystem::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Modules\ServiceAgreementSystem\View\Components\Layouts\Master::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    
    <div class="mb-6">
        <h2 style="font-size: 24px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px;">Daftar Tugas Anda</h2>
        <p class="text-muted" style="font-size: 14px;">Selesaikan semua tugas persetujuan dan verifikasi untuk memastikan kelancaran Service Agreement System.</p>
    </div>

    <?php
        $totalApprovals = $pendingApprovals->count();
        $totalLegalTasks = $legalTasks->count();
        $totalQcTasks = $qcTasks->count();
    ?>

    <?php if($totalApprovals === 0 && $totalLegalTasks === 0 && $totalQcTasks === 0): ?>
        <div class="empty-state card modern-card mt-4" style="padding: 40px;">
            <i class="fas fa-check-circle empty-icon text-success" style="font-size: 48px;"></i>
            <h4 style="margin-top: 16px; font-size: 18px; font-weight: 700;">Hore! Semua Tugas Selesai</h4>
            <p style="font-size: 14px; margin-top: 8px;">Anda tidak memiliki tugas persetujuan, review legal, maupun proses QC saat ini.</p>
        </div>
    <?php endif; ?>

    
    <?php if($totalApprovals > 0): ?>
        <details class="card modern-card mb-4" open>
            <summary class="card-header" style="cursor: pointer; user-select: none; list-style: none;">
                <div class="card-title d-flex align-items-center gap-2" style="font-size: 16px;">
                    <i class="fas fa-check-double text-primary"></i> 
                    Persetujuan USPK (Approval)
                    <span class="badge badge-pending" style="margin-left: 8px;"><?php echo e($totalApprovals); ?> Menunggu</span>
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
                            <?php $__currentLoopData = $pendingApprovals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uspk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-primary);"><?php echo e($uspk->uspk_number); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted);"><?php echo e($uspk->department->name ?? '-'); ?></div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo e($uspk->title); ?></div>
                                    </td>
                                    <td><?php echo e($uspk->submitter->name ?? '-'); ?></td>
                                    <td><?php echo e($uspk->created_at->format('d M Y')); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('sas.uspk.show', $uspk->id)); ?>" class="btn btn-primary btn-sm">
                                            Proses <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </details>
    <?php endif; ?>

    
    <?php if($totalLegalTasks > 0): ?>
        <details class="card modern-card mb-4" open>
            <summary class="card-header" style="cursor: pointer; user-select: none; list-style: none;">
                <div class="card-title d-flex align-items-center gap-2" style="font-size: 16px;">
                    <i class="fas fa-balance-scale text-warning"></i> 
                    Review Legal SPK
                    <span class="badge badge-warning" style="margin-left: 8px; background: rgba(245, 158, 11, 0.1); color: #d97706;"><?php echo e($totalLegalTasks); ?> Dokumen</span>
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
                            <?php $__currentLoopData = $legalTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uspk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-primary);"><?php echo e($uspk->uspk_number); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted);"><?php echo e($uspk->department->name ?? '-'); ?></div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo e($uspk->selectedTender->contractor->name ?? '-'); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted);">Rp <?php echo e(number_format($uspk->selectedTender->tender_value ?? 0, 0, ',', '.')); ?></div>
                                    </td>
                                    <td><?php echo e($uspk->submitter->name ?? '-'); ?></td>
                                    <td>
                                        <span class="badge badge-approved">Approved Final</span>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('sas.uspk.show', $uspk->id)); ?>" class="btn btn-warning btn-sm" style="color:#000;">
                                            Proses <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </details>
    <?php endif; ?>

    
    <?php if($totalQcTasks > 0): ?>
        <details class="card modern-card mb-4" open>
            <summary class="card-header" style="cursor: pointer; user-select: none; list-style: none;">
                <div class="card-title d-flex align-items-center gap-2" style="font-size: 16px;">
                    <i class="fas fa-clipboard-check" style="color: var(--info);"></i> 
                    Verifikasi QC Lapangan
                    <span class="badge badge-in_verification" style="margin-left: 8px;"><?php echo e($totalQcTasks); ?> Tugas</span>
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
                            <?php $__currentLoopData = $qcTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uspk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $qcStatusLabels = [
                                        \Modules\ServiceAgreementSystem\Models\UspkSubmission::QC_STATUS_PENDING_ASSIGNMENT => 'Tunggu Digassign',
                                        \Modules\ServiceAgreementSystem\Models\UspkSubmission::QC_STATUS_ASSIGNED => 'Tunggu Pekerjaan Selesai',
                                        \Modules\ServiceAgreementSystem\Models\UspkSubmission::QC_STATUS_IN_VERIFICATION => 'Perlu Verifikasi',
                                        \Modules\ServiceAgreementSystem\Models\UspkSubmission::QC_STATUS_REVISION_REQUIRED => 'Butuh Revisi (Rejected)',
                                    ];
                                ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-primary);"><?php echo e($uspk->uspk_number); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted);"><?php echo e($uspk->department->name ?? '-'); ?></div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo e($uspk->title); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">
                                            <?php echo e($qcStatusLabels[$uspk->qc_status] ?? $uspk->qc_status); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($uspk->submitter_signed_spk_uploaded_at?->format('d M Y') ?? '-'); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('sas.uspk.show', $uspk->id)); ?>" class="btn btn-info btn-sm" style="color:#fff; background:var(--info);">
                                            Proses <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </details>
    <?php endif; ?>

    <?php $__env->startPush('styles'); ?>
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
    <?php $__env->stopPush(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ffca6aaed06613cf5643e13e74c8806)): ?>
<?php $attributes = $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806; ?>
<?php unset($__attributesOriginal8ffca6aaed06613cf5643e13e74c8806); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ffca6aaed06613cf5643e13e74c8806)): ?>
<?php $component = $__componentOriginal8ffca6aaed06613cf5643e13e74c8806; ?>
<?php unset($__componentOriginal8ffca6aaed06613cf5643e13e74c8806); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/ServiceAgreementSystem\resources/views/uspk-task/index.blade.php ENDPATH**/ ?>