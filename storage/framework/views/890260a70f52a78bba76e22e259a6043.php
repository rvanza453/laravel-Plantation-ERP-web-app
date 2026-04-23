<?php if (isset($component)) { $__componentOriginal8ffca6aaed06613cf5643e13e74c8806 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806 = $attributes; } ?>
<?php $component = Modules\ServiceAgreementSystem\View\Components\Layouts\Master::resolve(['title' => 'Daftar Skema Approval'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('serviceagreementsystem::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Modules\ServiceAgreementSystem\View\Components\Layouts\Master::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="card">
        <div class="card-header">
            <div>
                <h1 class="card-title">Daftar Skema Approval</h1>
                <p class="text-muted" style="font-size: 13px; margin-top: 4px;">Manajemen rute persetujuan berlapis untuk pemrosesan USPK berdasarkan departemen.</p>
            </div>
            <a href="<?php echo e(route('sas.approval-schemas.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Skema
            </a>
        </div>
        
        <div class="card-body" style="padding: 0;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Skema</th>
                            <th>Departemen</th>
                            <th>Jumlah Tahap</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $schemas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schema): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div style="font-weight: 600; color: var(--text-primary);"><?php echo e($schema->name); ?></div>
                                <?php if($schema->description): ?>
                                    <div class="text-muted" style="font-size: 11px; margin-top: 2px;"><?php echo e(\Str::limit($schema->description, 50)); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php $__currentLoopData = $schema->departments->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge" style="background: var(--accent-light); color: var(--accent);"><?php echo e($dept->name); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($schema->departments->count() > 3): ?>
                                        <span class="badge" style="background: var(--bg-primary); color: var(--text-muted);">+<?php echo e($schema->departments->count() - 3); ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-draft"><?php echo e($schema->steps->count()); ?> Tahap</span>
                            </td>
                            <td>
                                <?php if($schema->is_active): ?>
                                    <span class="badge badge-approved">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-rejected">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <div class="d-flex justify-between" style="justify-content: flex-end; gap: 12px;">
                                    <a href="<?php echo e(route('sas.approval-schemas.edit', $schema)); ?>" class="btn btn-secondary btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('sas.approval-schemas.destroy', $schema)); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus skema ini?');" style="display: inline;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-folder-open"></i>
                                    <p class="mb-2">Belum ada Skema Approval</p>
                                    <p class="text-muted">Buat skema baru untuk mengatur rute persetujuan USPK.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/ServiceAgreementSystem\resources/views/approval-schema/index.blade.php ENDPATH**/ ?>