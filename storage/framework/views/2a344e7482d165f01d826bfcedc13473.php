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
    <div class="max-w-2xl">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit Block</h1>
            <p class="mt-1 text-sm text-gray-600">Perbarui informasi block <?php echo e($block->name); ?></p>
        </div>

        <!-- Error Messages -->
        <?php if($errors->any()): ?>
            <div class="mb-6 px-4 py-3 rounded-lg bg-red-100 border border-red-300">
                <h3 class="text-sm font-medium text-red-800 mb-2">Ada kesalahan:</h3>
                <ul class="text-sm text-red-700 space-y-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>• <?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" action="<?php echo e(route('admin.blocks.update', $block)); ?>" class="bg-white rounded-lg shadow">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="p-6 space-y-6">
                <!-- Afdeling Selection -->
                <div>
                    <label for="sub_department_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Afdeling <span class="text-red-600">*</span>
                    </label>
                    <select id="sub_department_id" name="sub_department_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 <?php $__errorArgs = ['sub_department_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="">-- Pilih Afdeling --</option>
                        <?php $__currentLoopData = $subDepartments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $siteName => $deptGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <optgroup label="<?php echo e($siteName); ?>">
                                <?php $__currentLoopData = $deptGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $afdeling): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($afdeling->id); ?>" 
                                            <?php if(old('sub_department_id', $block->sub_department_id) == $afdeling->id): echo 'selected'; endif; ?>>
                                        <?php echo e($afdeling->department->name); ?> - <?php echo e($afdeling->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </optgroup>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['sub_department_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Block Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Block <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="<?php echo e(old('name', $block->name)); ?>" required
                           placeholder="Contoh: Blok A1, Blok Utama, dsb" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Block Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                        Kode Block <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="code" name="code" value="<?php echo e(old('code', $block->code)); ?>" required
                           placeholder="Contoh: BLK-A1, A1, dsb" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <p class="mt-1 text-xs text-gray-500">Kode harus unik per afdeling</p>
                    <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Active Status -->
                <div>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                               <?php if(old('is_active', $block->is_active)): echo 'checked'; endif; ?>
                               class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Aktif</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Tandai jika block ini sedang aktif digunakan</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 justify-end px-6 py-4 border-t border-gray-200">
                <a href="<?php echo e(route('admin.blocks.index')); ?>" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
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
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\resources\views\admin\blocks\edit.blade.php ENDPATH**/ ?>