<?php if (isset($component)) { $__componentOriginal06cf778fd0d059309aebf5aee808823e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal06cf778fd0d059309aebf5aee808823e = $attributes; } ?>
<?php $component = Modules\PrSystem\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('prsystem::app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Modules\PrSystem\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h2>
            <a href="<?php echo e(route('users.create')); ?>" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 focus:bg-primary-500 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Tambah Pengguna
            </a>
        </div>

        <div class="space-y-4">
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $siteName => $siteUsers): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div x-data="{ open: true }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <button @click="open = !open" class="w-full flex justify-between items-center px-6 py-4 bg-gray-50 hover:bg-gray-100 transition-colors border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-800 text-lg"><?php echo e($siteName); ?></span>
                            <span class="bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full text-xs font-medium"><?php echo e($siteUsers->count()); ?> Pengguna</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transform transition-transform duration-200" style="width: 16px; height: 16px;" :class="{'rotate-180': open, 'rotate-0': !open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    
                    <div x-show="open" x-transition class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/5">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/5">Username</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/5">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/6">Role Global</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/6">Role Per Modul</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/6">Dept / Posisi</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-1/6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__currentLoopData = $siteUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($user->name); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($user->username ?? '-'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($user->email); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                    <?php echo e(ucfirst($role->name)); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($user->roles->isEmpty()): ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <?php if($user->moduleRoles->isEmpty()): ?>
                                                -
                                            <?php else: ?>
                                                <?php $__currentLoopData = $user->moduleRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $moduleRole): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-700 border border-slate-200 mr-1 mb-1">
                                                        <?php echo e(strtoupper($moduleRole->module_key)); ?>: <?php echo e($moduleRole->role_name); ?>

                                                    </span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="font-medium text-gray-700"><?php echo e($user->department->name ?? '-'); ?></div>
                                            <div class="text-xs text-gray-400"><?php echo e($user->position ?? '-'); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <div class="flex justify-center items-center gap-2">
                                                <form action="<?php echo e(route('users.impersonate', $user)); ?>" method="POST" class="inline" onsubmit="return confirmImpersonate(this, '<?php echo e($user->name); ?>')">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="admin_password" id="password-<?php echo e($user->id); ?>">
                                                    <button type="submit" class="p-1 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded" title="Login As">
                                                        <svg class="w-4 h-4" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    </button>
                                                </form>
                                                <a href="<?php echo e(route('users.edit', $user)); ?>" class="p-1 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded">
                                                    <svg class="w-4 h-4" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                                <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="p-1 text-red-600 hover:text-red-900 hover:bg-red-50 rounded">
                                                        <svg class="w-4 h-4" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="bg-white rounded-xl shadow-sm p-10 text-center text-gray-500">
                    Belum ada data pengguna.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function confirmImpersonate(form, userName) {
        const password = prompt(`Masukkan password verifikasi untuk login sebagai "${userName}":`);
        if (password === null) {
            return false; // User cancelled
        }
        form.querySelector('input[name="admin_password"]').value = password;
        return true;
    }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal06cf778fd0d059309aebf5aee808823e)): ?>
<?php $attributes = $__attributesOriginal06cf778fd0d059309aebf5aee808823e; ?>
<?php unset($__attributesOriginal06cf778fd0d059309aebf5aee808823e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal06cf778fd0d059309aebf5aee808823e)): ?>
<?php $component = $__componentOriginal06cf778fd0d059309aebf5aee808823e; ?>
<?php unset($__componentOriginal06cf778fd0d059309aebf5aee808823e); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/PrSystem\resources/views/admin/users/index.blade.php ENDPATH**/ ?>