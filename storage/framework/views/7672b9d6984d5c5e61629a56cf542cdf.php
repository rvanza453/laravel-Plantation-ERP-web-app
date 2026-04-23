<?php if (isset($component)) { $__componentOriginal8ffca6aaed06613cf5643e13e74c8806 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806 = $attributes; } ?>
<?php $component = Modules\ServiceAgreementSystem\View\Components\Layouts\Master::resolve(['title' => 'Persetujuan Saya'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('serviceagreementsystem::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Modules\ServiceAgreementSystem\View\Components\Layouts\Master::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php
        $totalPending = $pendingUspks->total();
    ?>

    <div class="approval-hero mb-4">
        <div class="hero-content">
            <h1 class="approval-title">Persetujuan Saya</h1>
            <p class="approval-subtitle">Daftar pengajuan USPK yang menunggu keputusan Anda.</p>
        </div>
        <div class="approval-counter">
            <div class="counter-value"><?php echo e($totalPending); ?></div>
            <div class="counter-label">Menunggu Proses</div>
        </div>
    </div>

    <?php if($pendingUspks->count() > 0): ?>
        <div class="approval-grid">
            <?php $__currentLoopData = $pendingUspks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uspk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="approval-card">
                    <div class="card-ribbon">
                        <i class="fas fa-exclamation-circle me-1"></i> Butuh Keputusan
                    </div>

                    <div class="card-top">
                        <a href="<?php echo e(route('sas.uspk.show', $uspk)); ?>" class="uspk-number">
                            <?php echo e($uspk->uspk_number); ?>

                        </a>
                        <span class="status-chip">Pending</span>
                    </div>

                    
                    <h3 class="work-title" title="<?php echo e($uspk->title); ?>">
                        <?php echo e($uspk->title); ?>

                    </h3>

                    <div class="meta-grid">
                        <div class="meta-item">
                            <div class="meta-label">Department</div>
                            <div class="meta-value"><?php echo e($uspk->department->name ?? '-'); ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Afdeling / Blok</div>
                            
                            <div class="meta-value d-flex align-items-start gap-1" title="<?php echo e($uspk->blocks->pluck('name')->join(', ')); ?>">
                                <span class="d-inline-block text-truncate" style="max-width: 100px;">
                                    <?php echo e($uspk->subDepartment->name ?? '-'); ?>

                                    <?php if($uspk->blocks && $uspk->blocks->count() > 0): ?>
                                        / <?php echo e($uspk->blocks->first()->name); ?>

                                    <?php endif; ?>
                                </span>
                                <?php if($uspk->blocks && $uspk->blocks->count() > 1): ?>
                                    <span class="badge rounded-pill" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-size: 0.65rem; padding: 0.2rem 0.4rem; margin-top: 0.1rem;">
                                        +<?php echo e($uspk->blocks->count() - 1); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Pengaju</div>
                            <div class="meta-value text-truncate" style="max-width: 120px;" title="<?php echo e($uspk->submitter->name ?? '-'); ?>">
                                <?php echo e($uspk->submitter->name ?? '-'); ?>

                            </div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Tanggal Ajuan</div>
                            <div class="meta-value"><?php echo e($uspk->created_at->format('d M Y')); ?></div>
                        </div>
                    </div>

                    <div class="budget-highlight">
                        <span class="text-muted">Estimasi Nilai</span>
                        <?php if((float) $uspk->estimated_value > 0): ?>
                            <strong class="font-monospace">Rp <?php echo e(number_format($uspk->estimated_value, 0, ',', '.')); ?></strong>
                        <?php else: ?>
                            <strong class="text-muted">-</strong>
                        <?php endif; ?>
                    </div>

                    <div class="card-actions mt-auto">
                        <a href="<?php echo e(route('sas.uspk.show', $uspk)); ?>" class="btn btn-primary btn-sm w-100 rounded-3 shadow-sm py-2">
                            <i class="fas fa-gavel me-1"></i> Tinjau & Proses
                        </a>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm rounded-4 text-center py-5 mt-4">
            <div class="empty-state">
                <div class="mb-3">
                    <i class="fas fa-check-circle fa-4x" style="color: #10b981; opacity: 0.2;"></i>
                    <i class="fas fa-check-circle fa-2x" style="color: #10b981; margin-top: -45px; position: absolute; margin-left: -35px;"></i>
                </div>
                <h4 class="mb-2" style="font-weight: 700; color: #0f172a;">Semua Selesai!</h4>
                <p class="text-muted mb-0">Tidak ada pengajuan USPK yang menunggu persetujuan Anda saat ini.</p>
            </div>
        </div>
    <?php endif; ?>

    <?php if($pendingUspks->hasPages()): ?>
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($pendingUspks->links()); ?>

        </div>
    <?php endif; ?>

    <?php $__env->startPush('styles'); ?>
    <style>
        .approval-hero {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 28px 32px;
            border-radius: 20px;
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 50%, #0f172a 100%);
            color: #f8fafc;
            box-shadow: 0 10px 25px -5px rgba(15, 118, 110, 0.4);
            position: relative;
            overflow: hidden;
        }

        /* Subtle background pattern/glow */
        .approval-hero::after {
            content: '';
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            background: radial-gradient(circle at top right, rgba(255,255,255,0.1) 0%, transparent 60%);
            pointer-events: none; 
        }

        .hero-content { position: relative; z-index: 1; }
        
        .approval-title {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
        }

        .approval-subtitle {
            margin: 0;
            color: rgba(248, 250, 252, 0.85);
            font-size: 15px;
        }

        .approval-counter {
            position: relative;
            z-index: 1;
            min-width: 160px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 16px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .counter-value {
            font-size: 36px;
            font-weight: 800;
            line-height: 1;
        }

        .counter-label {
            margin-top: 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(248, 250, 252, 0.9);
        }

        .approval-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .approval-card {
            position: relative;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .approval-card:hover {
            transform: translateY(-5px);
            border-color: #cbd5e1;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        .card-ribbon {
            position: absolute;
            top: -12px;
            right: 20px;
            background: #f59e0b;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 4px;
        }

        .uspk-number {
            color: #2563eb;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            background: #eff6ff;
            padding: 4px 10px;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .uspk-number:hover {
            background: #dbeafe;
        }

        .status-chip {
            background: #fef3c7;
            color: #d97706;
            border: 1px solid #fde68a;
            border-radius: 999px;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .work-title {
            font-size: 18px;
            font-weight: 700;
            line-height: 1.4;
            margin: 0;
            color: #0f172a;
            /* Memotong teks maksimal 2 baris */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 50px; 
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px 12px;
            border-top: 1px dashed #e2e8f0;
            border-bottom: 1px dashed #e2e8f0;
            padding: 16px 0;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .meta-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.05em;
        }

        .meta-value {
            font-size: 13px;
            color: #334155;
            font-weight: 600;
        }

        .budget-highlight {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 12px 16px;
        }

        .budget-highlight strong {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
        }

        @media (max-width: 768px) {
            .approval-hero {
                flex-direction: column;
                align-items: flex-start;
                padding: 24px;
            }
            .approval-counter {
                width: 100%;
            }
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
<?php endif; ?><?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/ServiceAgreementSystem\resources/views/uspk-approval/index.blade.php ENDPATH**/ ?>