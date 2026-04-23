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
<div class="space-y-6 max-w-5xl mx-auto py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                Manajemen Sistem (Maintenance)
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Aktifkan atau nonaktifkan modul sistem di halaman Hub Module untuk mempermudah perbaikan (maintenance).
            </p>
        </div>
        <div>
            <a href="<?php echo e(route('management.dashboard')); ?>" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-all duration-200">
                <svg class="mr-2 -ml-1 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Manajemen
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 mb-6 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800"><?php echo e(session('success')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900 leading-6">Status Portal Modul</h3>
            <p class="mt-1 text-sm text-gray-500">Apabila dinonaktifkan, user tidak akan dapat meng-klik dan masuk ke dalam portal bersangkutan dari halaman Hub Modul, berguna ketika tim IT butuh offline sementara untuk perbaikan database atau aplikasi.</p>
        </div>

        <ul role="list" class="divide-y divide-gray-200">
            <?php
                $modules = [
                    'sas' => ['name' => 'Service Agreement System', 'icon' => 'fa-screwdriver-wrench'],
                    'qc' => ['name' => 'QC Complaint System', 'icon' => 'fa-clipboard-check'],
                    'ispo' => ['name' => 'System ISPO', 'icon' => 'fa-leaf'],
                    'pr' => ['name' => 'Purchase Request System', 'icon' => 'fa-shopping-cart'],
                ];
            ?>
            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $mod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $isActive = $status[$key] ?? true;
            ?>
            <li class="p-6 hover:bg-gray-50 transition duration-150 ease-in-out">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center h-12 w-12 rounded-lg <?php echo e($isActive ? 'bg-indigo-100' : 'bg-gray-100'); ?> border border-indigo-200">
                                <i class="fa-solid <?php echo e($mod['icon']); ?> <?php echo e($isActive ? 'text-indigo-600' : 'text-gray-400'); ?> text-xl"></i>
                            </span>
                        </div>
                        <div>
                            <p class="text-base font-medium text-gray-900"><?php echo e($mod['name']); ?></p>
                            <p class="text-sm text-gray-500">Status saat ini: 
                                <?php if($isActive): ?>
                                    <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-md text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                        <svg class="h-1.5 w-1.5 fill-green-500" viewBox="0 0 6 6" aria-hidden="true"><circle cx="3" cy="3" r="3" /></svg> Active
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-md text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                        <svg class="h-1.5 w-1.5 fill-red-500" viewBox="0 0 6 6" aria-hidden="true"><circle cx="3" cy="3" r="3" /></svg> Disabled (Maintenance)
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div>
                        <form action="<?php echo e(route('admin.maintenance.toggle', $key)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="relative inline-flex flex-shrink-0 h-7 w-12 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 <?php echo e($isActive ? 'bg-indigo-600' : 'bg-gray-200'); ?>" role="switch" aria-checked="<?php echo e($isActive ? 'true' : 'false'); ?>">
                                <span class="sr-only">Use setting</span>
                                <!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
                                <span aria-hidden="true" class="pointer-events-none inline-block h-6 w-6 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 <?php echo e($isActive ? 'translate-x-5' : 'translate-x-0'); ?>"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-white">
            <h3 class="text-lg font-medium text-gray-900 leading-6">Laravel Cache Tools</h3>
            <p class="mt-1 text-sm text-gray-500">Gunakan tombol ini untuk membersihkan cache aplikasi, route, config, dan view tanpa SSH. Password verifikasi: <span class="font-semibold text-gray-900">tukangkebun123</span>.</p>
        </div>

        <div class="p-6 grid gap-6 md:grid-cols-2">
            <div class="rounded-xl border border-indigo-200 bg-indigo-50/50 p-4">
                <h4 class="text-base font-semibold text-indigo-900">Clear Cache</h4>
                <p class="mt-1 text-sm text-indigo-800">Membersihkan cache Laravel, route cache, config cache, dan compiled view cache. Ini aman untuk data.</p>
                <form action="<?php echo e(route('system.reset-warehouse.post')); ?>" method="POST" class="mt-4 space-y-4" onsubmit="return confirm('Bersihkan cache Laravel sekarang? Ini tidak menghapus data aplikasi.');">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="clear_cache">
                    <div>
                        <label for="admin_password_clear_cache" class="block text-sm font-medium text-gray-700 mb-1">Password Verifikasi Admin</label>
                        <input type="password" name="admin_password" id="admin_password_clear_cache" required
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               placeholder="Masukkan password verifikasi">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Clear Cache
                        </button>
                    </div>
                </form>
            </div>

            <div class="rounded-xl border border-red-200 bg-red-50/60 p-4">
                <h4 class="text-base font-semibold text-red-900">Reset Warehouse</h4>
                <p class="mt-1 text-sm text-red-800">Fitur lama untuk reset data warehouse dan budget usage tetap tersedia di sini.</p>
                <form action="<?php echo e(route('system.reset-warehouse.post')); ?>" method="POST" class="mt-4 space-y-4" onsubmit="return confirm('APAKAH ANDA YAKIN INGIN MERESET SEMUA DATA WAREHOUSE DAN BUDGET? TINDAKAN INI TIDAK BISA DIBATALKAN!');">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="reset_warehouse">
                    <div>
                        <label for="admin_password_reset_warehouse" class="block text-sm font-medium text-gray-700 mb-1">Password Verifikasi Admin</label>
                        <input type="password" name="admin_password" id="admin_password_reset_warehouse" required
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                               placeholder="Masukkan password verifikasi">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Eksekusi Reset Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\resources\views\admin\maintenance\index.blade.php ENDPATH**/ ?>