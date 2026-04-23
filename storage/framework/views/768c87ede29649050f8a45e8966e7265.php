<?php if (isset($component)) { $__componentOriginal8ffca6aaed06613cf5643e13e74c8806 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806 = $attributes; } ?>
<?php $component = Modules\ServiceAgreementSystem\View\Components\Layouts\Master::resolve(['title' => 'Data Kontraktor'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('serviceagreementsystem::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Modules\ServiceAgreementSystem\View\Components\Layouts\Master::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('actions'); ?>
        <a href="<?php echo e(route('sas.contractors.create')); ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Kontraktor
        </a>
    <?php $__env->stopPush(); ?>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Daftar Kontraktor</div>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Perusahaan</th>
                        <th>NPWP</th>
                        <th>Telepon</th>
                        <th>Bank</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $contractors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contractor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td style="font-weight: 600; color: var(--text-primary);"><?php echo e($contractor->name); ?></td>
                        <td><?php echo e($contractor->company_name ?? '-'); ?></td>
                        <td><?php echo e($contractor->npwp ?? '-'); ?></td>
                        <td><?php echo e($contractor->phone ?? '-'); ?></td>
                        <td><?php echo e($contractor->bank_name ?? '-'); ?></td>
                        <td>
                            <span class="badge <?php echo e($contractor->is_active ? 'badge-approved' : 'badge-rejected'); ?>">
                                <?php echo e($contractor->is_active ? 'Aktif' : 'Nonaktif'); ?>

                            </span>
                        </td>
                        <td class="text-right">
                            <div class="d-flex gap-2" style="justify-content: flex-end;">
                                <a href="<?php echo e(route('sas.contractors.show', $contractor)); ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('sas.contractors.edit', $contractor)); ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('sas.contractors.destroy', $contractor)); ?>" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-hard-hat"></i>
                                <p>Belum ada data kontraktor.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($contractors->hasPages()): ?>
        <div class="pagination-wrapper">
            <?php echo e($contractors->links()); ?>

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
<?php endif; ?>
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/ServiceAgreementSystem\resources/views/contractor/index.blade.php ENDPATH**/ ?>