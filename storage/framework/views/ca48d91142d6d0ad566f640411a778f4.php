<?php if (isset($component)) { $__componentOriginal8ffca6aaed06613cf5643e13e74c8806 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806 = $attributes; } ?>
<?php $component = Modules\ServiceAgreementSystem\View\Components\Layouts\Master::resolve(['title' => 'Manajemen BAPP'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('serviceagreementsystem::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Modules\ServiceAgreementSystem\View\Components\Layouts\Master::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('styles'); ?>
    <style>
        .bapp-table-wrapper {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
            border: 1px solid rgba(226,232,240,0.8);
            overflow: hidden;
        }
        .bapp-header {
            padding: 24px 32px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafafa;
        }
        .bapp-title { font-weight: 800; font-size: 18px; display: flex; align-items: center; gap: 10px; color: #0f172a; }
        .bapp-table { width: 100%; border-collapse: collapse; }
        .bapp-table th { background: #f8fafc; padding: 16px 24px; font-size: 12px; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; text-align: left;}
        .bapp-table td { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .bapp-table tr:hover td { background: #f8fafc; }
        .badge-premium { background: #eff6ff; color: #3b82f6; padding: 4px 10px; border-radius: 20px; font-weight: 600; font-size: 12px; }
        .btn-premium { background: #3b82f6; color: white; padding: 10px 20px; border-radius: 10px; font-weight: 600; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;}
        .btn-premium:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(37,99,235,0.2); color:white;}
        .btn-outline { border: 1px solid #e2e8f0; color: #475569; padding: 8px 16px; border-radius: 8px; transition: 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap:6px; font-weight: 500;}
        .btn-outline:hover { background: #f1f5f9; color: #0f172a; }
    </style>
    <?php $__env->stopPush(); ?>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        
        <?php if(session('success')): ?>
        <div class="mb-6 p-4 rounded-xl" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-check-circle" style="font-size: 20px;"></i>
            <span style="font-weight: 500;"><?php echo e(session('success')); ?></span>
        </div>
        <?php endif; ?>

        <div class="bapp-table-wrapper">
            <div class="bapp-header">
                <div class="bapp-title">
                    <i class="fas fa-file-invoice-dollar" style="color: #10b981; background: #ecfdf5; padding: 10px; border-radius: 10px;"></i>
                    Arsip BAPP (Pembayaran)
                </div>
                <a href="<?php echo e(route('sas.bapp.create')); ?>" class="btn-premium">
                    <i class="fas fa-plus"></i> Buat BAPP Baru
                </a>
            </div>

            <table class="bapp-table">
                <thead>
                    <tr>
                        <th>No. BAPP</th>
                        <th>Tanggal</th>
                        <th>File Arsip</th>
                        <th>Kontraktor</th>
                        <th>Pekerjaan</th>
                        <th>Total SPK</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $bapps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bapp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td style="font-weight: 700; color: #1e293b;"><?php echo e($bapp->bapp_number); ?></td>
                        <td style="color: #64748b;"><?php echo e($bapp->bapp_date->format('d M Y')); ?></td>
                        <td>
                            <?php if($bapp->document_link): ?>
                            <a href="<?php echo e($bapp->document_link); ?>" target="_blank" class="badge-premium" style="background: #fef2f2; color: #ef4444; border: 1px solid #fca5a5;">
                                <i class="fas fa-external-link-alt"></i> Lihat BAPP
                            </a>
                            <?php else: ?>
                            <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #334155;"><?php echo e($bapp->contractor->name ?? 'Multi Kontraktor'); ?></div>
                        </td>
                        <td style="color: #475569;"><?php echo e($bapp->job->name ?? '-'); ?></td>
                        <td>
                            <span class="badge-premium"><?php echo e($bapp->submissions_count); ?> SPK/USPK</span>
                        </td>
                        <td>
                            <a href="<?php echo e(route('sas.bapp.show', $bapp->id)); ?>" class="btn-outline">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 60px 20px;">
                            <i class="fas fa-folder-open" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
                            <h3 style="font-size: 18px; font-weight: 600; color: #0f172a; margin-bottom: 8px;">Belum Ada Arsip BAPP</h3>
                            <p style="color: #64748b; max-width: 400px; margin: 0 auto;">Semua SPK yang telah lolos QC dan dilakukan pembayaran offline dapat diarsip ke dalam sistem di sini.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <?php if($bapps->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($bapps->links()); ?>

            </div>
            <?php endif; ?>
        </div>
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
<?php endif; ?>
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/ServiceAgreementSystem\resources/views/bapp/index.blade.php ENDPATH**/ ?>