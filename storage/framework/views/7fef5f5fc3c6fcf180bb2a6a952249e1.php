<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name')); ?> - Master Admin</title>
    
        <link rel="icon" href="<?php echo e(asset('favicon.ico')); ?>" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        
        
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <style>
            .ts-control { border-radius: 0.375rem; border-color: #d1d5db; padding: 0.5rem; }
            .ts-wrapper.single .ts-control { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }

            /* Keep wide tables/forms usable on small screens without breaking layout. */
            .admin-mobile-scroll { max-width: 100%; }
            @media (max-width: 767px) {
                .admin-mobile-scroll {
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#f3f4f6] pb-16 md:pb-0">
        <?php echo $__env->make('components.impersonation-banner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('components.module-hub-button', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        
        <div class="min-h-screen flex">
            <!-- Admin Sidebar -->
            <aside class="w-64 bg-[#fff8e1] border-r border-gray-200 flex-shrink-0 hidden md:block" style="background-color: #fdfbf7;">
                <div class="h-16 flex items-center px-6 border-b border-gray-100">
                    <!-- Logo -->
                    <div class="flex items-center gap-2 text-primary-700 font-bold text-lg">
                       <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                       Master Admin
                    </div>
                </div>

                <div class="p-4 border-b border-gray-100 mb-4">
                     <a href="<?php echo e(route('profile.edit')); ?>" class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg transition-colors cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-gray-200 group-hover:bg-primary-100 flex items-center justify-center text-gray-500 group-hover:text-primary-600 transition-colors">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="overflow-hidden">
                            <div class="text-sm font-medium text-gray-900 truncate group-hover:text-primary-700"><?php echo e(Auth::user()->name); ?></div>
                            <div class="text-xs text-gray-500 truncate"><?php echo e(Auth::user()->email); ?></div>
                        </div>
                     </a>
                </div>

                <nav class="px-4 space-y-1">
                    <div class="px-4 py-2 mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        Master Data & Settings
                    </div>

                    <a href="<?php echo e(route('admin.users.index')); ?>" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200 <?php echo e(request()->routeIs('admin.users.*') ? 'bg-white shadow-sm text-primary-600' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1m0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span class="text-sm font-medium">Manajemen Pengguna</span>
                    </a>

                    <a href="<?php echo e(route('admin.sites.index')); ?>" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200 <?php echo e(request()->routeIs('admin.sites.*') ? 'bg-white shadow-sm text-primary-600' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a2 2 0 010-2.828l4.243-4.243a8 8 0 10.001 11.314z"/></svg>
                        <span class="text-sm font-medium">Master Site</span>
                    </a>

                    <a href="<?php echo e(route('admin.master-departments.index')); ?>" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200 <?php echo e(request()->routeIs('admin.master-departments.*') ? 'bg-white shadow-sm text-primary-600' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/></svg>
                        <span class="text-sm font-medium">Master Unit</span>
                    </a>

                    <a href="<?php echo e(route('admin.departments.index')); ?>" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200 <?php echo e(request()->routeIs('admin.departments.*') ? 'bg-white shadow-sm text-primary-600' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 16l4-16M6 9h12M4 15h12"/></svg>
                        <span class="text-sm font-medium">Master Department</span>
                    </a>

                    <a href="<?php echo e(route('admin.sub-departments.index')); ?>" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200 <?php echo e(request()->routeIs('admin.sub-departments.*') ? 'bg-white shadow-sm text-primary-600' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg>
                        <span class="text-sm font-medium">Master Sub Department</span>
                    </a>
                    
                        <a href="<?php echo e(route('admin.blocks.index')); ?>" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200 <?php echo e(request()->routeIs('admin.blocks.*') ? 'bg-white shadow-sm text-primary-600' : ''); ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3m0 2V5m0 10l-3-3m0 0l-3 3m3-3v3"/></svg>
                            <span class="text-sm font-medium">Master Block</span>
                        </a>

                    <a href="<?php echo e(route('admin.activity-logs.index')); ?>" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200 <?php echo e(request()->routeIs('admin.activity-logs.*') ? 'bg-white shadow-sm text-primary-600' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">Log Aktivitas</span>
                    </a>

                    <div class="pt-4 mt-4 border-t border-gray-100">
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </nav>

                <div class="mt-auto px-6 py-4 border-t border-gray-100">
                    <div class="text-xs text-gray-400">
                        &copy; <?php echo e(date('Y')); ?> <a href="https://github.com/rvanza453" target="_blank" class="hover:text-indigo-600 transition-colors">revanza</a>
                    </div>
                </div>
            </aside>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <div class="py-4 px-4 md:py-6 md:px-8">
                     <?php if(session('success')): ?>
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline"><?php echo e(session('success')); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if(session('error')): ?>
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline"><?php echo e(session('error')); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="admin-mobile-scroll">
                        <?php echo e($slot); ?>

                    </div>
                </div>
            </main>
        </div>

        <!-- Mobile Bottom Navigation -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-300 z-30 shadow-lg">
            <div class="flex justify-around items-center h-16 px-2">
                <a href="<?php echo e(route('admin.users.index')); ?>" class="flex flex-col items-center justify-center flex-1 py-2 <?php echo e(request()->routeIs('admin.users.*') ? 'text-indigo-600' : 'text-gray-600'); ?>">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1m0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="text-xs mt-1 font-medium">Users</span>
                </a>
                <a href="<?php echo e(route('admin.sites.index')); ?>" class="flex flex-col items-center justify-center flex-1 py-2 <?php echo e(request()->routeIs('admin.sites.*') ? 'text-indigo-600' : 'text-gray-600'); ?>">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a2 2 0 010-2.828l4.243-4.243a8 8 0 10.001 11.314z"/></svg>
                    <span class="text-xs mt-1 font-medium">Sites</span>
                </a>
                <a href="<?php echo e(route('admin.departments.index')); ?>" class="flex flex-col items-center justify-center flex-1 py-2 <?php echo e(request()->routeIs('admin.departments.*') ? 'text-indigo-600' : 'text-gray-600'); ?>">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 16l4-16M6 9h12M4 15h12"/></svg>
                    <span class="text-xs mt-1 font-medium">Dept</span>
                </a>
                <a href="<?php echo e(route('admin.activity-logs.index')); ?>" class="flex flex-col items-center justify-center flex-1 py-2 <?php echo e(request()->routeIs('admin.activity-logs.*') ? 'text-indigo-600' : 'text-gray-600'); ?>">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-xs mt-1 font-medium">Log</span>
                </a>
            </div>
        </nav>
    </body>
</html>
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\resources\views/layouts/admin-layout.blade.php ENDPATH**/ ?>