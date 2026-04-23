<?php if (isset($component)) { $__componentOriginal91fdd17964e43374ae18c674f95cdaa3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal91fdd17964e43374ae18c674f95cdaa3 = $attributes; } ?>
<?php $component = App\View\Components\AdminLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AdminLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="space-y-6">
        <h2 class="text-2xl font-bold text-gray-800">Log Aktivitas</h2>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sistem</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan Aktivitas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Route</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo e($activity->user->name ?? 'System'); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold <?php echo e(($activity->system ?? 'OTHER') === 'SAS' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'); ?>">
                                    <?php echo e($activity->system ?? 'OTHER'); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-medium"><?php echo e($activity->action); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($activity->description); ?></td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                <div><?php echo e($activity->route_name ?? '-'); ?></div>
                                <div class="mt-1">
                                    <span class="inline-flex items-center rounded px-1.5 py-0.5 bg-gray-100 text-gray-700 font-semibold"><?php echo e(strtoupper($activity->http_method ?? '-')); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($activity->created_at->format('d M Y H:i')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada log aktivitas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div><?php echo e($activities->links()); ?></div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal91fdd17964e43374ae18c674f95cdaa3)): ?>
<?php $attributes = $__attributesOriginal91fdd17964e43374ae18c674f95cdaa3; ?>
<?php unset($__attributesOriginal91fdd17964e43374ae18c674f95cdaa3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal91fdd17964e43374ae18c674f95cdaa3)): ?>
<?php $component = $__componentOriginal91fdd17964e43374ae18c674f95cdaa3; ?>
<?php unset($__componentOriginal91fdd17964e43374ae18c674f95cdaa3); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\resources\views\admin\activity-logs\index.blade.php ENDPATH**/ ?>