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
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Master Department (Unit)</h2>
            <a href="<?php echo e(route('admin.master-departments.create')); ?>" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 transition">
                + Tambah Unit
            </a>
        </div>

        <?php if(session('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(isset($sites)): ?>
            <p class="text-gray-600">Pilih site untuk melihat unit di dalamnya.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('admin.master-departments.index', ['site_id' => $site->id])); ?>" class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition border hover:border-indigo-400">
                        <h3 class="font-bold text-gray-800"><?php echo e($site->name); ?></h3>
                        <p class="text-sm text-gray-600"><?php echo e($site->departments_count); ?> unit</p>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php elseif(isset($site)): ?>
            <div class="flex items-center gap-2 mb-4">
                <a href="<?php echo e(route('admin.master-departments.index')); ?>" class="text-indigo-600 hover:text-indigo-900">&larr; Kembali</a>
                <span class="text-gray-600">Site: <strong><?php echo e($site->name); ?></strong></span>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">COA</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sub Dept</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo e($dept->name); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($dept->coa); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($dept->subDepartments->count()); ?></td>
                                    <td class="px-6 py-4 text-sm space-x-2">
                                        <a href="<?php echo e(route('admin.master-departments.edit', $dept)); ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form method="POST" action="<?php echo e(route('admin.master-departments.destroy', $dept)); ?>" style="display:inline;" onsubmit="return confirm('Yakin dihapus?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada unit.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div><?php echo e($departments->links()); ?></div>
        <?php endif; ?>
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
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\resources\views\admin\master-departments\index.blade.php ENDPATH**/ ?>