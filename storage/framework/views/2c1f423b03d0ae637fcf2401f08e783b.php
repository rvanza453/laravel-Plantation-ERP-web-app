<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Form Pengambilan Sampling - Lab Dashboard</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        /* Sembunyikan scrollbar untuk form mobile tapi tetap bisa di-scroll */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#E6F0F9] font-sans antialiased overflow-x-hidden min-h-screen selection:bg-[#4FA5F5] selection:text-white">

    <div class="fixed top-0 left-0 w-72 h-72 sm:w-96 sm:h-96 bg-[#D4E8F9] rounded-full mix-blend-multiply filter blur-3xl opacity-70 -z-10 pointer-events-none"></div>
    <div class="fixed bottom-0 right-0 sm:right-10 w-64 h-64 sm:w-72 sm:h-72 bg-[#D4E8F9] rounded-full mix-blend-multiply filter blur-3xl opacity-70 -z-10 pointer-events-none"></div>

    <div class="mx-auto max-w-[1400px] px-3 py-6 sm:px-6 lg:py-10 relative z-10 pb-32 sm:pb-10">
        
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6 sm:mb-8 bg-white/60 backdrop-blur-md p-4 sm:pr-6 rounded-[24px] sm:rounded-[30px] border border-white shadow-sm">
            <div class="flex items-center gap-4">
                <a href="<?php echo e(route('lab.dashboard')); ?>" class="flex h-12 w-12 sm:h-14 sm:w-14 flex-shrink-0 items-center justify-center rounded-full bg-white text-gray-500 shadow-sm transition-all hover:scale-105 hover:text-[#FF6B93]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5 sm:h-6 sm:w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight">Pengambilan Sampling</h1>
                    <p class="text-xs sm:text-sm font-medium text-gray-500 mt-0.5">Petugas: <span class="text-[#4FA5F5]"><?php echo e(auth()->user()->name ?? 'Administrator'); ?></span></p>
                </div>
            </div>
            
            <div class="flex w-full lg:w-auto rounded-full bg-white/80 p-1 sm:p-1.5 shadow-inner border border-gray-100">
                <button onclick="switchTab('satuan')" id="btn-satuan" class="flex-1 lg:flex-none px-4 sm:px-6 py-2.5 rounded-full text-xs sm:text-sm font-extrabold transition-all bg-[#4FA5F5] text-white shadow-md">
                    Mode Satuan
                </button>
                <button onclick="switchTab('batch')" id="btn-batch" class="flex-1 lg:flex-none px-4 sm:px-6 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all text-gray-500 hover:text-gray-900 hover:bg-gray-50">
                    Mode Batch
                </button>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-5 sm:mb-6 rounded-[20px] sm:rounded-[24px] border border-[#52D3D8]/30 bg-[#52D3D8]/10 p-4 sm:p-5 text-sm font-bold text-[#209CA1] shadow-sm backdrop-blur-sm">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mb-5 sm:mb-6 rounded-[20px] sm:rounded-[24px] border border-[#FF6B93]/30 bg-[#FFE5EE] p-4 sm:p-5 text-sm text-[#E03A69] shadow-sm backdrop-blur-sm">
                <p class="font-extrabold mb-2 text-base">Periksa data berikut:</p>
                <ul class="list-inside list-disc space-y-1 font-medium">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div id="tab-satuan" class="space-y-6 transition-all duration-500 block">
            
            <?php
                $parameters = collect($parameters ?? []);
                $todayEntries = collect($todayEntries ?? []);
                $groupedParameters = $parameters->groupBy(fn ($item) => $item->category ?? 'Lainnya');
                $groupedHistory = $todayEntries->groupBy(['shift', 'category']);

                $totalInput = $todayEntries->count();
                $uniqueParameter = $todayEntries->pluck('parameter_id')->filter()->unique()->count();
            ?>

            <button onclick="toggleMobileForm(true)" class="fixed bottom-6 right-5 z-30 lg:hidden flex items-center justify-center gap-2 rounded-full bg-[#FF6B93] px-5 py-4 text-sm font-extrabold text-white shadow-lg shadow-[#FF6B93]/40 transition-transform active:scale-95">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                <span>Input Data</span>
            </button>

            <div class="grid grid-cols-1 gap-4 sm:gap-5 sm:grid-cols-3">
                <div class="rounded-[20px] sm:rounded-[30px] border border-white bg-white/90 p-5 sm:p-6 shadow-[0_8px_20px_-6px_rgba(0,0,0,0.03)] backdrop-blur-xl flex flex-row sm:flex-col items-center sm:items-start justify-between">
                    <p class="text-xs sm:text-sm font-extrabold text-gray-400 uppercase tracking-wider">Tanggal Shift</p>
                    <p class="text-xl sm:text-2xl font-black text-gray-900 mt-0 sm:mt-2"><?php echo e(now()->translatedFormat('d M Y')); ?></p>
                </div>
                <div class="rounded-[20px] sm:rounded-[30px] border border-white bg-white/90 p-5 sm:p-6 shadow-[0_8px_20px_-6px_rgba(0,0,0,0.03)] backdrop-blur-xl flex flex-row sm:flex-col items-center sm:items-start justify-between">
                    <p class="text-xs sm:text-sm font-extrabold text-gray-400 uppercase tracking-wider">Input Hari Ini</p>
                    <p class="text-xl sm:text-3xl font-black text-gray-900 mt-0 sm:mt-2"><?php echo e(number_format($totalInput)); ?> <span class="text-sm sm:text-lg text-gray-400 font-bold">Data</span></p>
                </div>
                <div class="rounded-[20px] sm:rounded-[30px] border border-white bg-white/90 p-5 sm:p-6 shadow-[0_8px_20px_-6px_rgba(0,0,0,0.03)] backdrop-blur-xl flex flex-row sm:flex-col items-center sm:items-start justify-between">
                    <p class="text-xs sm:text-sm font-extrabold text-gray-400 uppercase tracking-wider">Parameter Terisi</p>
                    <p class="text-xl sm:text-3xl font-black text-gray-900 mt-0 sm:mt-2"><?php echo e(number_format($uniqueParameter)); ?> <span class="text-sm sm:text-lg text-gray-400 font-bold">Jenis</span></p>
                </div>
            </div>

            
            <div class="rounded-[24px] sm:rounded-[40px] border border-white bg-white/90 p-5 sm:p-6 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.05)] backdrop-blur-xl">
                
                
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="h-6 w-6 rounded-full bg-[#FCE57F] flex items-center justify-center text-gray-900 font-black shadow-sm">!</div>
                            <h2 class="text-lg sm:text-xl font-extrabold text-gray-900 tracking-tight">Quest Global & Daftar Misi</h2>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500 max-w-2xl">Semua target misi (shift maupun harian) yang Anda selesaikan di bawah ini berkontribusi langsung pada pencapaian Quest Global.</p>
                    </div>
                </div>

                
                <div class="mb-6">
                    <?php if($questTotal === 0): ?>
                        
                        <div class="rounded-[18px] border border-dashed border-amber-200 bg-amber-50 p-4">
                            <p class="text-sm font-semibold text-amber-800">
                                ⏸️ Quest belum dimulai. Mulai dengan input data pertama untuk memulai perhitungan quest.
                            </p>
                        </div>
                    <?php else: ?>
                        
                        <div class="rounded-[20px] bg-[#E6F0F9] px-5 py-5 space-y-3 shadow-inner">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-700">Progress Kumulatif Total</span>
                                <span class="text-2xl font-black text-[#4FA5F5]"><?php echo e(number_format($questCompleted ?? 0)); ?>/<?php echo e(number_format($questTotal ?? 0)); ?></span>
                            </div>
                            <div class="h-4 rounded-full bg-white overflow-hidden shadow-sm border border-[#4FA5F5]/20">
                                <div class="h-full rounded-full bg-gradient-to-r from-[#4FA5F5] to-[#10B981] transition-all duration-500 relative" style="width: <?php echo e(min(100, round((($questCompleted ?? 0) / $questTotal) * 100))); ?>%">
                                    <div class="absolute inset-0 bg-white/20" style="background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent); background-size: 1rem 1rem;"></div>
                                </div>
                            </div>
                            <p class="text-right text-xs font-bold text-gray-500">
                                <?php echo e($questTotal > 0 ? round((($questCompleted ?? 0) / $questTotal) * 100) : 0); ?>% selesai
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5">
                    
                    
                    <div class="rounded-[24px] border border-blue-100 bg-gradient-to-br from-white to-blue-50/30 p-4 sm:p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-sm sm:text-base font-extrabold text-blue-900 flex items-center gap-2">
                                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-500 text-white text-xs font-black">⚡</span>
                                    Shift Missions
                                </h3>
                                <p class="text-xs text-blue-600 mt-0.5">Personal • Per 2-4 Jam</p>
                            </div>
                        </div>

                        <div class="space-y-2.5 max-h-[360px] overflow-y-auto pr-1 no-scrollbar">
                            <?php
                                $shiftMissions = collect($missionGroups ?? [])->get('Target Per 2 Jam', collect());
                                $pendingMissions = $shiftMissions->where('mission_completed', false);
                                $completedMissions = $shiftMissions->where('mission_completed', true);
                            ?>
                            
                            
                            <?php $__empty_1 = true; $__currentLoopData = $pendingMissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $frequency = $mission->sampling_frequency ?? '-';
                                    $resetLabel = $mission->mission_reset_label ?? '-';
                                ?>
                                <div class="flex items-start gap-3 rounded-[18px] border border-blue-100 px-3 py-2.5 bg-slate-50 hover:bg-blue-50 transition-colors cursor-pointer" data-mission-shortcut data-parameter-name="<?php echo e($mission->parameter_name); ?>" data-parameter-id="<?php echo e($mission->parameter_id ?? ''); ?>">
                                    <div class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white border border-blue-300 text-blue-300">
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold text-gray-800"><?php echo e($mission->parameter_name); ?></p>
                                        <div class="mt-1 flex flex-wrap items-center gap-2 text-[10px] font-semibold">
                                            <span class="rounded-full bg-white px-2 py-0.5 text-gray-500 border border-blue-100"><?php echo e($mission->category); ?> • <?php echo e($frequency); ?></span>
                                            <span
                                                class="rounded-full px-2 py-0.5 bg-blue-100 text-blue-800"
                                                data-mission-reset-countdown
                                                data-reset-at="<?php echo e($mission->mission_reset_iso); ?>"
                                                data-mission-done="0"
                                                data-mission-prefix="Tersedia"
                                            >
                                                Tersedia • reset <?php echo e($resetLabel); ?>

                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="rounded-[18px] border border-dashed border-blue-200 bg-blue-50/50 px-4 py-3 text-sm text-blue-400">Tidak ada target per 2 jam yang belum selesai.</div>
                            <?php endif; ?>

                            
                            <?php if($completedMissions->isNotEmpty()): ?>
                                <div class="mt-4 pt-3 border-t-2 border-emerald-200"></div>
                                <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider px-1 mb-2">✓ Sudah Selesai</div>
                                <?php $__currentLoopData = $completedMissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $frequency = $mission->sampling_frequency ?? '-';
                                        $resetLabel = $mission->mission_reset_label ?? '-';
                                    ?>
                                    <div class="flex items-start gap-3 rounded-[18px] border border-emerald-100 px-3 py-2.5 bg-emerald-50/60">
                                        <div class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.414l-7.01 7.01a1 1 0 01-1.414 0L3.296 8.82a1 1 0 111.414-1.414l3.273 3.273 6.303-6.303a1 1 0 011.418-.086z" clip-rule="evenodd"/></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-bold text-emerald-700 line-through decoration-emerald-500/70"><?php echo e($mission->parameter_name); ?></p>
                                            <div class="mt-1 flex flex-wrap items-center gap-2 text-[10px] font-semibold">
                                                <span class="rounded-full bg-white px-2 py-0.5 text-gray-500 border border-emerald-100"><?php echo e($mission->category); ?> • <?php echo e($frequency); ?></span>
                                                <span class="rounded-full px-2 py-0.5 bg-emerald-100 text-emerald-700">
                                                    Selesai • reset <?php echo e($resetLabel); ?>

                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="rounded-[24px] border-2 border-purple-200 bg-gradient-to-br from-white to-purple-50/30 p-4 sm:p-5 shadow-md">
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-sm sm:text-base font-extrabold text-purple-900 flex items-center gap-2">
                                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-purple-500 text-white text-xs font-black">🏆</span>
                                    Daily Shared Missions
                                </h3>
                                <p class="text-xs text-purple-600 mt-0.5">Shared Accountability • Shift 1 & 2</p>
                            </div>
                        </div>

                        <div class="space-y-2.5 max-h-[360px] overflow-y-auto pr-1 no-scrollbar">
                            <?php
                                $dailyMissions = collect($missionGroups ?? [])->get('Target Harian', collect());
                                $pendingDailyMissions = $dailyMissions->where('mission_completed', false);
                                $completedDailyMissions = $dailyMissions->where('mission_completed', true);
                            ?>
                            
                            
                            <?php $__empty_1 = true; $__currentLoopData = $pendingDailyMissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $frequency = $mission->sampling_frequency ?? '-';
                                    $resetLabel = $mission->mission_reset_label ?? '-';
                                ?>
                                <div class="flex items-start gap-3 rounded-[18px] border border-purple-100 px-3 py-2.5 bg-slate-50 hover:bg-purple-50 transition-colors cursor-pointer" data-mission-shortcut data-parameter-name="<?php echo e($mission->parameter_name); ?>" data-parameter-id="<?php echo e($mission->parameter_id ?? ''); ?>">
                                    <div class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white border border-purple-300 text-purple-300">
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold text-gray-800"><?php echo e($mission->parameter_name); ?></p>
                                        <div class="mt-1 flex flex-wrap items-center gap-2 text-[10px] font-semibold">
                                            <span class="rounded-full bg-white px-2 py-0.5 text-gray-500 border border-purple-100"><?php echo e($mission->category); ?> • <?php echo e($frequency); ?></span>
                                            <span class="rounded-full px-2 py-0.5 bg-purple-100 text-purple-800">
                                                Pending • reset <?php echo e($resetLabel); ?>

                                            </span>
                                        </div>
                                        <p class="text-[10px] text-purple-500 font-semibold mt-2">⚠️ Tanggung jawab Shift 1 & 2. Jika belum selesai saat Shift 2 berakhir, mendapat penalty.</p>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="rounded-[18px] border border-dashed border-purple-200 bg-purple-50/50 px-4 py-3 text-sm text-purple-400">Tidak ada target harian yang belum selesai.</div>
                            <?php endif; ?>

                            
                            <?php if($completedDailyMissions->isNotEmpty()): ?>
                                <div class="mt-4 pt-3 border-t-2 border-emerald-200"></div>
                                <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider px-1 mb-2">✓ Sudah Selesai</div>
                                <?php $__currentLoopData = $completedDailyMissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $frequency = $mission->sampling_frequency ?? '-';
                                    ?>
                                    <div class="flex items-start gap-3 rounded-[18px] border border-emerald-100 px-3 py-2.5 bg-emerald-50/60">
                                        <div class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.414l-7.01 7.01a1 1 0 01-1.414 0L3.296 8.82a1 1 0 111.414-1.414l3.273 3.273 6.303-6.303a1 1 0 011.418-.086z" clip-rule="evenodd"/></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-bold text-emerald-700 line-through decoration-emerald-500/70"><?php echo e($mission->parameter_name); ?></p>
                                            <div class="mt-1 flex flex-wrap items-center gap-2 text-[10px] font-semibold">
                                                <span class="rounded-full bg-white px-2 py-0.5 text-gray-500 border border-emerald-100"><?php echo e($mission->category); ?> • <?php echo e($frequency); ?></span>
                                                <span class="rounded-full px-2 py-0.5 bg-emerald-100 text-emerald-700">
                                                    Selesai ✓
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <div class="mt-6 border-t border-gray-100 pt-5">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">📋 Info Sistem & Tanggung Jawab Progress</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-gray-600">
                        <div class="flex gap-2 bg-gray-50 p-2 rounded-lg">
                            <span class="font-bold text-[#FF6B93] min-w-[100px]">Mulai Hitung:</span>
                            <span>Sejak data pertama diinput hari ini</span>
                        </div>
                        <div class="flex gap-2 bg-gray-50 p-2 rounded-lg">
                            <span class="font-bold text-[#4FA5F5] min-w-[100px]">Kunci Waktu:</span>
                            <span>2 jam window (tidak bisa isi ulang)</span>
                        </div>
                        <div class="flex gap-2 bg-gray-50 p-2 rounded-lg">
                            <span class="font-bold text-[#10B981] min-w-[100px]">Hak Akses:</span>
                            <span>Semua petugas global tanpa kecuali</span>
                        </div>
                        <div class="flex gap-2 bg-gray-50 p-2 rounded-lg">
                            <span class="font-bold text-[#52D3D8] min-w-[100px]">Status Misi:</span>
                            <span>Tersimpan di kumulatif meski reset</span>
                        </div>
                    </div>
                </div>

                
                <div class="mt-8 flex justify-end">
                    <button type="button" onclick="prepareCloseDailySession()" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-full bg-gradient-to-r from-orange-500 to-red-500 px-8 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-red-500/30 hover:shadow-red-500/50 transition-all active:scale-95">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        <span>🛑 Tutup Sesi Harian</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 relative mt-6">
                
                <div id="mobile-backdrop" class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden" onclick="toggleMobileForm(false)"></div>

                <div id="form-container" class="fixed inset-x-0 bottom-0 z-50 translate-y-full transform transition-transform duration-300 ease-out lg:static lg:z-auto lg:translate-y-0 lg:col-span-4 lg:block">
                    
                    <form id="satuan-form" action="<?php echo e(route('lab.sampling.store')); ?>" method="POST" class="rounded-t-[32px] lg:rounded-[40px] border border-white bg-white/95 lg:bg-white/90 p-5 sm:p-8 shadow-[0_-10px_40px_rgba(0,0,0,0.1)] lg:shadow-[0_20px_50px_-12px_rgba(0,0,0,0.05)] backdrop-blur-2xl space-y-4 sm:space-y-5 lg:sticky lg:top-6 max-h-[85vh] overflow-y-auto no-scrollbar lg:max-h-none lg:overflow-visible">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="input_mode" value="satuan">

                        <div class="flex items-center justify-between mb-2 lg:mb-0">
                            <div>
                                <div class="flex items-center gap-3 mb-1 sm:mb-2">
                                    <div class="h-5 sm:h-6 w-1.5 rounded-full bg-[#FF6B93]"></div>
                                    <h2 class="text-lg sm:text-xl font-extrabold text-gray-900">Tambah Komponen</h2>
                                </div>
                                <p class="text-xs sm:text-sm font-medium text-gray-500 hidden lg:block mb-4 sm:mb-6">Isi satu parameter lalu simpan.</p>
                            </div>
                            <button type="button" onclick="toggleMobileForm(false)" class="lg:hidden flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-500 active:bg-gray-200">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <div>
                            <label class="mb-1.5 sm:mb-2 block text-xs sm:text-sm font-extrabold text-gray-700 ml-1 sm:ml-2">Sumber Sampel</label>
                            <input type="text" name="source_unit" value="<?php echo e(old('source_unit', $defaultSourceUnit ?? 'PKS SSM')); ?>" placeholder="Contoh: PKS SSM" class="w-full rounded-[16px] sm:rounded-full border-none bg-[#F3F6F9] px-4 sm:px-5 py-3.5 text-sm font-bold text-gray-900 focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" required>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-1 gap-3 sm:gap-4">
                            <div>
                                <label class="mb-1.5 sm:mb-2 block text-xs sm:text-sm font-extrabold text-gray-700 ml-1 sm:ml-2">Pilih Parameter</label>
                                <select id="satuan-parameter-select" name="parameter_id" class="w-full rounded-[16px] sm:rounded-full border-none bg-[#F3F6F9] px-4 sm:px-5 py-3.5 text-sm font-bold text-gray-900 focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20 cursor-pointer" required onchange="handleSatuanSelectChange()">
                                    <option value="" data-category-key="general">-- Pilih Komponen --</option>
                                    <?php $__currentLoopData = $groupedParameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <optgroup label="<?php echo e($category); ?>" class="font-extrabold text-gray-500 bg-white">
                                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $catLow = strtolower($item->category ?? '');
                                                    $isTot = str_contains(strtolower($item->parameter_name ?? ''), 'total');
                                                ?>
                                                <?php if(!$isTot): ?>
                                                    <option value="<?php echo e($item->id); ?>" data-category-key="<?php echo e($catLow); ?>" <?php if((int) old('parameter_id') === (int) $item->id): echo 'selected'; endif; ?> class="text-gray-900 font-bold">
                                                        <?php echo e($item->parameter_name); ?> (<?php echo e($item->sampling_frequency ?: 'N/A'); ?>)
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </optgroup>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        
                        <input type="hidden" id="hidden_measured_time" name="measured_time">
                        
                        <div id="satuan-input-ffa" class="hidden space-y-3">
                            <label class="mb-1.5 sm:mb-2 block text-xs sm:text-sm font-extrabold text-gray-700 ml-1 sm:ml-2">Variabel Uji FFA</label>
                            <div class="rounded-[24px] bg-[#F7F9FC] p-4 border border-gray-50 space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="mb-1 block text-[10px] sm:text-xs font-extrabold text-gray-500 uppercase tracking-wider">Berat Sample (A)</label>
                                        <input type="text" inputmode="decimal" id="satuan_ffa_a" name="formula[a]" value="<?php echo e(old('formula.a')); ?>" class="w-full rounded-[14px] border-none bg-white px-4 py-3 text-sm font-bold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" placeholder="0.00" disabled>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[10px] sm:text-xs font-extrabold text-gray-500 uppercase tracking-wider">Vol Titrasi (B)</label>
                                        <input type="text" inputmode="decimal" id="satuan_ffa_b" name="formula[b]" value="<?php echo e(old('formula.b')); ?>" class="w-full rounded-[14px] border-none bg-white px-4 py-3 text-sm font-bold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" placeholder="0.00" disabled>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[10px] sm:text-xs font-extrabold text-gray-500 uppercase tracking-wider">Normalitas (C)</label>
                                        <input type="text" inputmode="decimal" id="satuan_ffa_c" name="formula[c]" value="<?php echo e(old('formula.c')); ?>" class="w-full rounded-[14px] border-none bg-white px-4 py-3 text-sm font-bold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" placeholder="0.00" disabled>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="rounded-[16px] bg-white px-4 py-3 border border-gray-100">
                                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-gray-500">Konstanta D</p>
                                        <p class="mt-1 text-lg font-black text-gray-900">25.6</p>
                                    </div>
                                    <div class="rounded-[16px] bg-white px-4 py-3 border border-[#4FA5F5]/20">
                                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-[#4FA5F5]">Final FFA (%)</p>
                                        <input type="text" inputmode="decimal" readonly id="satuan_ffa_result" name="measured_value" class="mt-1 w-full border-none bg-transparent p-0 text-2xl font-black text-gray-900 focus:outline-none" disabled>
                                    </div>
                                </div>
                                <p class="text-[10px] sm:text-xs font-medium text-gray-500">Formula: (B × C × 25.6) / A</p>
                            </div>
                        </div>

                        <div id="satuan-input-dirt" class="hidden space-y-3">
                            <label class="mb-1.5 sm:mb-2 block text-xs sm:text-sm font-extrabold text-gray-700 ml-1 sm:ml-2">Variabel Uji Dirt</label>
                            <div class="rounded-[24px] bg-[#F7F9FC] p-4 border border-gray-50 space-y-3">
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <div>
                                        <label class="mb-1 block text-[10px] sm:text-xs font-extrabold text-gray-500 uppercase tracking-wider">B. Wadah (L)</label>
                                        <input type="text" inputmode="decimal" id="satuan_dirt_l" name="formula[l]" value="<?php echo e(old('formula.l')); ?>" class="w-full rounded-[14px] border-none bg-white px-4 py-3 text-sm font-bold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" placeholder="0.00" disabled>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[10px] sm:text-xs font-extrabold text-gray-500 uppercase tracking-wider">W+Sampel (M)</label>
                                        <input type="text" inputmode="decimal" id="satuan_dirt_m" name="formula[m]" value="<?php echo e(old('formula.m')); ?>" class="w-full rounded-[14px] border-none bg-white px-4 py-3 text-sm font-bold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" placeholder="0.00" disabled>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[10px] sm:text-xs font-extrabold text-gray-500 uppercase tracking-wider">Cr. Kosong (O)</label>
                                        <input type="text" inputmode="decimal" id="satuan_dirt_o" name="formula[o]" value="<?php echo e(old('formula.o')); ?>" class="w-full rounded-[14px] border-none bg-white px-4 py-3 text-sm font-bold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" placeholder="0.00" disabled>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[10px] sm:text-xs font-extrabold text-gray-500 uppercase tracking-wider">Cr. + Dirt (P)</label>
                                        <input type="text" inputmode="decimal" id="satuan_dirt_p" name="formula[p]" value="<?php echo e(old('formula.p')); ?>" class="w-full rounded-[14px] border-none bg-white px-4 py-3 text-sm font-bold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" placeholder="0.00" disabled>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="rounded-[16px] bg-white px-4 py-3 border border-gray-100">
                                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-gray-500">Berat Sampel (N)</p>
                                        <p class="mt-1 text-base font-black text-gray-900" id="satuan_dirt_n">0.00</p>
                                    </div>
                                    <div class="rounded-[16px] bg-white px-4 py-3 border border-gray-100">
                                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-gray-500">Berat Kotoran (Q)</p>
                                        <p class="mt-1 text-base font-black text-gray-900" id="satuan_dirt_q">0.00</p>
                                    </div>
                                    <div class="rounded-[16px] bg-white px-4 py-3 border border-[#4FA5F5]/20">
                                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-[#4FA5F5]">Final % Dirt</p>
                                        <input type="text" inputmode="decimal" readonly id="satuan_dirt_result" name="measured_value" class="mt-1 w-full border-none bg-transparent p-0 text-2xl font-black text-gray-900 focus:outline-none" disabled>
                                    </div>
                                </div>
                                <p class="text-[10px] sm:text-xs font-medium text-gray-500">Formula: ((P - O) / (M - L)) × 100</p>
                            </div>
                        </div>

                        <div id="satuan-input-generic" class="hidden space-y-3">
                            <label class="mb-1.5 sm:mb-2 block text-xs sm:text-sm font-extrabold text-gray-700 ml-1 sm:ml-2">Nilai Uji Parameter</label>
                            <input type="text" inputmode="decimal" id="satuan_generic_result" name="measured_value" class="w-full rounded-[16px] border-none bg-[#F3F6F9] px-4 py-4 text-base font-black text-gray-900 shadow-inner focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" placeholder="Masukkan nilai..." disabled>
                        </div>

                        <div>
                            <label class="mb-1.5 sm:mb-2 block text-xs sm:text-sm font-extrabold text-gray-700 ml-1 sm:ml-2">Catatan</label>
                            <textarea name="notes" rows="2" placeholder="Keterangan tambahan..." class="w-full rounded-[16px] sm:rounded-[24px] border-none bg-[#F3F6F9] px-4 sm:px-5 py-3 sm:py-3.5 text-sm font-bold text-gray-900 focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20 resize-none"><?php echo e(old('notes')); ?></textarea>
                        </div>

                        <button type="submit" class="mt-4 flex w-full items-center justify-center gap-2 rounded-[16px] sm:rounded-full bg-[#4FA5F5] px-5 sm:px-6 py-4 text-sm font-extrabold text-white shadow-lg shadow-[#4FA5F5]/30 transition-all hover:bg-[#3b8fdc] active:scale-95">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            <span>Simpan ke Histori</span>
                        </button>
                        <div class="h-6 lg:hidden"></div>
                    </form>
                </div>

                <div class="lg:col-span-8">
                    <div class="flex items-center justify-between mb-4 sm:mb-6 px-1">
                        <div>
                            <h2 class="text-lg sm:text-2xl font-extrabold text-gray-900">Histori Hari Ini</h2>
                            <p class="text-xs sm:text-sm font-medium text-gray-500 mt-1">Data dikelompokkan per kategori untuk kenyamanan baca.</p>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-[#FCEB8C] px-3 py-1 text-[10px] sm:text-xs font-extrabold text-gray-800 shadow-sm">Real-time</span>
                    </div>

                    <?php if($groupedHistory->isEmpty()): ?>
                        <div class="rounded-[24px] border border-white bg-white/90 p-10 text-center shadow-[0_20px_50px_-12px_rgba(0,0,0,0.05)] backdrop-blur-xl">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#E6F0F9] text-[#4FA5F5] mb-4">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            </div>
                            <p class="text-lg font-extrabold text-gray-900">Belum Ada Data</p>
                            <p class="mt-1 text-sm font-medium text-gray-500">Mulai input parameter menggunakan form.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-8 sm:space-y-10">
                            <?php $__currentLoopData = $groupedHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift => $categories): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div>
                                    <div class="flex items-center gap-3 mb-4 pl-1">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#4FA5F5] text-white font-black shadow-md"><?php echo e($shift); ?></span>
                                        <h3 class="text-xl font-black text-gray-900 tracking-tight">Shift <?php echo e($shift); ?></h3>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 items-start">
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $entries): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="rounded-[20px] sm:rounded-[24px] border border-white bg-white/95 p-4 sm:p-5 shadow-[0_10px_30px_-10px_rgba(0,0,0,0.05)] hover:shadow-[0_15px_35px_-10px_rgba(0,0,0,0.08)] transition-all">
                                                
                                                <div class="flex items-center gap-2 mb-4 border-b border-gray-100 pb-3">
                                                    <div class="h-2 w-2 rounded-full bg-[#FF6B93]"></div>
                                                    <h4 class="text-xs sm:text-sm font-extrabold text-[#FF6B93] uppercase tracking-wider"><?php echo e($category); ?></h4>
                                                </div>
                                                
                                                <div class="space-y-3">
                                                    <?php $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="flex items-center justify-between group">
                                                            <div class="flex items-center gap-3 overflow-hidden pr-2">
                                                                <span class="text-[10px] sm:text-xs font-bold text-gray-400 w-8 sm:w-10 shrink-0">
                                                                    <?php echo e(\Carbon\Carbon::parse($entry->measured_at ?? now())->format('H:i')); ?>

                                                                </span>
                                                                <span class="text-xs sm:text-sm font-bold text-gray-700 truncate leading-tight group-hover:text-[#4FA5F5] transition-colors">
                                                                    <?php echo e($entry->parameter_name); ?>

                                                                </span>
                                                            </div>
                                                            <div class="flex items-end gap-1.5 shrink-0 bg-[#F3F6F9] px-2 py-1 rounded-lg">
                                                                <span class="text-sm sm:text-base font-black text-gray-900">
                                                                    <?php echo e($entry->measured_text ?: $entry->measured_value ?: '-'); ?>

                                                                </span>
                                                                <span class="text-[9px] sm:text-[10px] font-extrabold text-gray-500 mb-0.5 w-5 sm:w-6 truncate">
                                                                    <?php echo e($entry->unit); ?>

                                                                </span>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>

                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div id="tab-batch" class="hidden transition-all duration-500">
            <div class="max-w-3xl mx-auto">
                <form action="<?php echo e(route('lab.sampling.store')); ?>" method="POST" class="space-y-6 sm:space-y-8">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="input_mode" value="batch">

                    <div class="rounded-[24px] sm:rounded-[40px] border border-white bg-white/90 p-5 sm:p-8 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.05)] backdrop-blur-xl space-y-4 sm:space-y-6">
                        <div>
                            <div class="flex items-center gap-2 sm:gap-3 mb-1 sm:mb-2">
                                <div class="h-5 sm:h-6 w-1.5 rounded-full bg-[#FCE57F]"></div>
                                <h2 class="text-lg sm:text-xl font-extrabold text-gray-900">Informasi Umum (Batch)</h2>
                            </div>
                            <p class="text-xs sm:text-sm font-medium text-gray-500 mb-3 sm:mb-4">Cocok untuk input gelondongan seluruh data di akhir shift.</p>
                        </div>
                        
                        <div>
                            <label class="mb-1.5 sm:mb-2 block text-xs sm:text-sm font-extrabold text-gray-700 ml-1 sm:ml-2">Unit Sumber Sampel</label>
                            <input type="text" name="source_unit" value="<?php echo e(old('source_unit', $defaultSourceUnit ?? 'PKS SSM')); ?>" placeholder="Contoh: PKS SSM" class="w-full rounded-[16px] sm:rounded-full border-none bg-[#F3F6F9] px-4 sm:px-6 py-3 sm:py-4 text-sm sm:text-base font-bold text-gray-900 transition-shadow focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20 focus:bg-white" required>
                        </div>
                    </div>

                    <?php $__empty_1 = true; $__currentLoopData = $groupedParameters ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="space-y-3 sm:space-y-4">
                            <div class="flex items-center gap-2 sm:gap-3 px-1 sm:px-2">
                                <div class="h-6 sm:h-8 w-2 rounded-full bg-[#4FA5F5]"></div>
                                <h2 class="text-lg sm:text-xl font-extrabold text-gray-900 tracking-tight"><?php echo e($category); ?></h2>
                            </div>
                            
                            <div class="rounded-[24px] sm:rounded-[40px] border border-white bg-white/90 p-3 sm:p-6 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.05)] backdrop-blur-xl space-y-3 sm:space-y-4">
                                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $categoryLower = strtolower($item->category ?? '');
                                        $nameLower = strtolower($item->parameter_name ?? '');
                                        $isFfa = $categoryLower === 'ffa';
                                        $isDirt = $categoryLower === 'dirt';
                                        $isTotal = str_contains($nameLower, 'total');
                                        $isFormula = $isFfa || $isDirt;
                                        $directValue = old('measurements.' . $item->id);
                                    ?>

                                    <div
                                        class="rounded-[20px] sm:rounded-[24px] border <?php echo e($isTotal ? 'border-[#4FA5F5] bg-[#E6F0F9]/50' : 'border-gray-50 bg-[#F7F9FC]'); ?> p-4 sm:p-5 transition-all hover:bg-white hover:shadow-md hover:ring-2 hover:ring-[#4FA5F5]/20 group"
                                        data-parameter-card
                                        data-category="<?php echo e($category); ?>"
                                        data-category-key="<?php echo e($categoryLower); ?>"
                                        data-parameter-id="<?php echo e($item->id); ?>"
                                        data-parameter-name="<?php echo e($item->parameter_name); ?>"
                                        data-is-total="<?php echo e($isTotal ? '1' : '0'); ?>"
                                    >
                                        <div class="mb-3 sm:mb-4 flex flex-col sm:flex-row sm:items-start justify-between gap-2 sm:gap-3">
                                            <div>
                                                <p class="text-base sm:text-lg font-bold <?php echo e($isTotal ? 'text-[#4FA5F5]' : 'text-gray-900'); ?> group-hover:text-[#4FA5F5] transition-colors leading-tight">
                                                    <?php echo e($item->parameter_name); ?>

                                                    <?php if($isTotal): ?>
                                                        <span class="ml-2 text-[10px] bg-[#4FA5F5] text-white px-2 py-0.5 rounded-md font-extrabold">AUTO</span>
                                                    <?php endif; ?>
                                                </p>
                                                <div class="mt-1.5 flex flex-wrap items-center gap-1.5 sm:gap-2 text-[10px] sm:text-xs font-medium text-gray-400">
                                                    <span class="bg-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md shadow-sm border border-gray-100">STD: <span class="text-gray-700 font-bold"><?php echo e($item->standard_text ?? '-'); ?></span></span>
                                                    <span class="bg-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md shadow-sm border border-gray-100">Freq: <span class="text-gray-700 font-bold"><?php echo e($item->sampling_frequency ?? '-'); ?></span></span>
                                                </div>
                                            </div>
                                            <span class="self-start sm:self-auto inline-flex items-center justify-center rounded-lg sm:rounded-xl bg-white border border-gray-100 min-w-[2.5rem] px-2 sm:px-3 py-1 text-[10px] sm:text-xs font-black text-[#FF6B93] shadow-sm">
                                                <?php echo e($item->unit ?? 'N/A'); ?>

                                            </span>
                                        </div>

                                        <?php if($isFfa): ?>
                                            <div class="space-y-3">
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                    <div>
                                                        <label class="mb-1 block text-[10px] sm:text-xs font-extrabold text-gray-500 uppercase tracking-wider">Berat Sample (A)</label>
                                                        <input type="text" inputmode="decimal" data-ffa-a name="formula[<?php echo e($item->id); ?>][a]" value="<?php echo e(old('formula.' . $item->id . '.a')); ?>" class="w-full rounded-[14px] border-none bg-white px-4 py-3 text-sm font-bold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" placeholder="0.00">
                                                    </div>
                                                    <div>
                                                        <label class="mb-1 block text-[10px] sm:text-xs font-extrabold text-gray-500 uppercase tracking-wider">Volume Titrasi (B)</label>
                                                        <input type="text" inputmode="decimal" data-ffa-b name="formula[<?php echo e($item->id); ?>][b]" value="<?php echo e(old('formula.' . $item->id . '.b')); ?>" class="w-full rounded-[14px] border-none bg-white px-4 py-3 text-sm font-bold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" placeholder="0.00">
                                                    </div>
                                                    <div>
                                                        <label class="mb-1 block text-[10px] sm:text-xs font-extrabold text-gray-500 uppercase tracking-wider">Normalitas Larutan (C)</label>
                                                        <input type="text" inputmode="decimal" data-ffa-c name="formula[<?php echo e($item->id); ?>][c]" value="<?php echo e(old('formula.' . $item->id . '.c')); ?>" class="w-full rounded-[14px] border-none bg-white px-4 py-3 text-sm font-bold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div class="rounded-[16px] bg-white px-4 py-3 border border-gray-100">
                                                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-gray-500">Konstanta D</p>
                                                        <p class="mt-1 text-lg font-black text-gray-900">25.6</p>
                                                    </div>
                                                    <div class="rounded-[16px] bg-white px-4 py-3 border border-[#4FA5F5]/20">
                                                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-[#4FA5F5]">Final FFA</p>
                                                        <input type="text" inputmode="decimal" readonly name="measurements[<?php echo e($item->id); ?>]" value="<?php echo e(old('measurements.' . $item->id)); ?>" data-formula-result data-formula-type="ffa" class="mt-1 w-full border-none bg-transparent p-0 text-2xl font-black text-gray-900 focus:outline-none">
                                                    </div>
                                                </div>
                                                <p class="text-[10px] sm:text-xs font-medium text-gray-500">Formula: (B × C × 25.6) / A</p>
                                            </div>
                                        <?php elseif($isDirt): ?>
                                            <div class="space-y-3">
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                                    <div>
                                                        <label class="mb-1 block text-[10px] sm:text-xs font-extrabold text-gray-500 uppercase tracking-wider">Berat Wadah (L)</label>
                                                        <input type="text" inputmode="decimal" data-dirt-l name="formula[<?php echo e($item->id); ?>][l]" value="<?php echo e(old('formula.' . $item->id . '.l')); ?>" class="w-full rounded-[14px] border-none bg-white px-4 py-3 text-sm font-bold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" placeholder="0.00">
                                                    </div>
                                                    <div>
                                                        <label class="mb-1 block text-[10px] sm:text-xs font-extrabold text-gray-500 uppercase tracking-wider">W+Sample (M)</label>
                                                        <input type="text" inputmode="decimal" data-dirt-m name="formula[<?php echo e($item->id); ?>][m]" value="<?php echo e(old('formula.' . $item->id . '.m')); ?>" class="w-full rounded-[14px] border-none bg-white px-4 py-3 text-sm font-bold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" placeholder="0.00">
                                                    </div>
                                                    <div>
                                                        <label class="mb-1 block text-[10px] sm:text-xs font-extrabold text-gray-500 uppercase tracking-wider">Crucible Kosong (O)</label>
                                                        <input type="text" inputmode="decimal" data-dirt-o name="formula[<?php echo e($item->id); ?>][o]" value="<?php echo e(old('formula.' . $item->id . '.o')); ?>" class="w-full rounded-[14px] border-none bg-white px-4 py-3 text-sm font-bold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" placeholder="0.00">
                                                    </div>
                                                    <div>
                                                        <label class="mb-1 block text-[10px] sm:text-xs font-extrabold text-gray-500 uppercase tracking-wider">Crucible+Dirt (P)</label>
                                                        <input type="text" inputmode="decimal" data-dirt-p name="formula[<?php echo e($item->id); ?>][p]" value="<?php echo e(old('formula.' . $item->id . '.p')); ?>" class="w-full rounded-[14px] border-none bg-white px-4 py-3 text-sm font-bold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/20" placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                    <div class="rounded-[16px] bg-white px-4 py-3 border border-gray-100">
                                                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-gray-500">Berat Sample (N)</p>
                                                        <p class="mt-1 text-base font-black text-gray-900" data-dirt-n>0.00</p>
                                                    </div>
                                                    <div class="rounded-[16px] bg-white px-4 py-3 border border-gray-100">
                                                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-gray-500">Berat Kotoran (Q)</p>
                                                        <p class="mt-1 text-base font-black text-gray-900" data-dirt-q>0.00</p>
                                                    </div>
                                                    <div class="rounded-[16px] bg-white px-4 py-3 border border-[#4FA5F5]/20">
                                                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-[#4FA5F5]">Final % Dirt</p>
                                                        <input type="text" inputmode="decimal" readonly name="measurements[<?php echo e($item->id); ?>]" value="<?php echo e(old('measurements.' . $item->id)); ?>" data-formula-result data-formula-type="dirt" class="mt-1 w-full border-none bg-transparent p-0 text-2xl font-black text-gray-900 focus:outline-none">
                                                    </div>
                                                </div>
                                                <p class="text-[10px] sm:text-xs font-medium text-gray-500">Formula: ((P - O) / (M - L)) × 100</p>
                                            </div>
                                        <?php elseif($isTotal): ?>
                                            <div class="space-y-2">
                                                <div class="rounded-[16px] bg-white px-4 py-3 border border-[#FCE57F]/40">
                                                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-[#D2A100]">Auto Sum</p>
                                                    <input type="text" readonly inputmode="decimal" name="measurements[<?php echo e($item->id); ?>]" value="<?php echo e(old('measurements.' . $item->id)); ?>" data-total-result data-total-category="<?php echo e($categoryLower); ?>" class="mt-1 w-full border-none bg-transparent p-0 text-2xl font-black text-gray-900 focus:outline-none">
                                                </div>
                                                <p class="text-[10px] sm:text-xs font-medium text-gray-500">Total dihitung otomatis dari parameter lain pada kategori ini.</p>
                                            </div>
                                        <?php else: ?>
                                            <div class="relative">
                                                <input type="text" inputmode="decimal" name="measurements[<?php echo e($item->id); ?>]" value="<?php echo e($directValue); ?>" placeholder="Hasil ukur..." data-direct-result data-category-key="<?php echo e($categoryLower); ?>" class="w-full rounded-[16px] sm:rounded-2xl border-none bg-white px-4 sm:px-5 py-3 sm:py-4 text-sm sm:text-base font-bold text-gray-900 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] transition-shadow placeholder:text-gray-300 focus:outline-none focus:ring-4 focus:ring-[#4FA5F5]/30 focus:bg-white">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div id="sticky-bar" class="fixed sm:sticky bottom-0 sm:bottom-4 left-0 w-full z-40 sm:z-20 pointer-events-none transition-all duration-500">
        <div class="mx-auto max-w-[1400px] px-0 sm:px-6 pointer-events-auto">
            <div id="action-batch" class="hidden rounded-t-[24px] sm:rounded-[30px] bg-white/90 p-4 sm:p-4 backdrop-blur-xl shadow-[0_-10px_40px_-10px_rgba(0,0,0,0.1)] border border-white max-w-3xl mx-auto">
                <div class="flex flex-col-reverse sm:flex-row gap-3 sm:gap-4">
                    <button type="button" onclick="switchTab('satuan')" class="inline-flex w-full sm:w-1/3 items-center justify-center rounded-[16px] sm:rounded-full bg-[#F3F6F9] px-5 py-3.5 sm:py-4 text-sm font-extrabold text-gray-600 transition-all hover:bg-gray-200">Batal</button>
                    <button type="button" onclick="document.querySelector('#tab-batch form').submit()" class="inline-flex w-full sm:w-2/3 items-center justify-center gap-2 rounded-[16px] sm:rounded-full bg-[#FCE57F] px-5 py-3.5 sm:py-4 text-sm font-extrabold text-gray-900 shadow-lg shadow-[#FCE57F]/30 transition-all hover:-translate-y-1 hover:shadow-xl active:scale-95">
                        <span>Submit Semua Data</span>
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Logika Pindah Tab
        function switchTab(tabId) {
            try {
                const tabSatuan = document.getElementById('tab-satuan');
                const tabBatch = document.getElementById('tab-batch');
                const btnSatuan = document.getElementById('btn-satuan');
                const btnBatch = document.getElementById('btn-batch');
                const actionBatch = document.getElementById('action-batch');

                if (!tabSatuan || !tabBatch || !btnSatuan || !btnBatch || !actionBatch) {
                    console.warn('Some tabs or buttons not found');
                    return;
                }

                if (tabId === 'satuan') {
                    tabSatuan.classList.remove('hidden');
                    tabBatch.classList.add('hidden');
                    actionBatch.classList.add('hidden');
                    btnSatuan.className = "flex-1 lg:flex-none px-4 sm:px-6 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-extrabold transition-all bg-[#4FA5F5] text-white shadow-md";
                    btnBatch.className = "flex-1 lg:flex-none px-4 sm:px-6 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all text-gray-500 hover:text-gray-900 hover:bg-gray-50";
                } else {
                    tabBatch.classList.remove('hidden');
                    tabSatuan.classList.add('hidden');
                    actionBatch.classList.remove('hidden');
                    btnBatch.className = "flex-1 lg:flex-none px-4 sm:px-6 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-extrabold transition-all bg-[#4FA5F5] text-white shadow-md";
                    btnSatuan.className = "flex-1 lg:flex-none px-4 sm:px-6 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all text-gray-500 hover:text-gray-900 hover:bg-gray-50";
                }
            } catch (err) {
                console.error('Error switching tabs:', err);
            }
        }

        // Logika Pop-Up Form (Mobile Bottom Sheet)
        function toggleMobileForm(show) {
            const backdrop = document.getElementById('mobile-backdrop');
            const formContainer = document.getElementById('form-container');
            
            if (show) {
                backdrop.classList.remove('pointer-events-none');
                backdrop.classList.remove('opacity-0');
                formContainer.classList.remove('translate-y-full');
                document.body.style.overflow = 'hidden';
            } else {
                backdrop.classList.add('opacity-0');
                backdrop.classList.add('pointer-events-none');
                formContainer.classList.add('translate-y-full');
                document.body.style.overflow = '';
            }
        }

        // FUNGSI UMUM UNTUK PARSING ANGKA
        function parseSamplingNumber(value) {
            const normalized = String(value ?? '').replace(',', '.').trim();
            if (normalized === '') return null;
            const number = Number(normalized);
            return Number.isFinite(number) ? number : null;
        }

        function formatSamplingNumber(value, decimals = 4) {
            if (value === null || value === undefined || Number.isNaN(value) || !Number.isFinite(value)) return '';
            return Number(value).toFixed(decimals).replace(/\.0+$/, '').replace(/(\.[0-9]*?)0+$/, '$1');
        }

        // FUNGSI UNTUK MODE BATCH (Bawaan Anda sebelumnya)
        function calculateSamplingCards() {
            // Kalkulasi Formula FFA & Dirt di Tab Batch
            document.querySelectorAll('#tab-batch [data-parameter-card]').forEach((card) => {
                const resultInput = card.querySelector('[data-formula-result]');
                const isFfa = !!card.querySelector('[data-ffa-a]');
                const isDirt = !!card.querySelector('[data-dirt-l]');

                if (isFfa && resultInput) {
                    const sampleWeight = parseSamplingNumber(card.querySelector('[data-ffa-a]')?.value);
                    const titrationVolume = parseSamplingNumber(card.querySelector('[data-ffa-b]')?.value);
                    const normality = parseSamplingNumber(card.querySelector('[data-ffa-c]')?.value);

                    let finalValue = null;
                    if (sampleWeight && sampleWeight !== 0 && titrationVolume !== null && normality !== null) {
                        finalValue = (titrationVolume * normality * 25.6) / sampleWeight;
                    }
                    resultInput.value = formatSamplingNumber(finalValue, 4);
                }

                if (isDirt && resultInput) {
                    const containerWeight = parseSamplingNumber(card.querySelector('[data-dirt-l]')?.value);
                    const withSampleWeight = parseSamplingNumber(card.querySelector('[data-dirt-m]')?.value);
                    const crucibleEmpty = parseSamplingNumber(card.querySelector('[data-dirt-o]')?.value);
                    const crucibleWithDirt = parseSamplingNumber(card.querySelector('[data-dirt-p]')?.value);
                    const sampleOutput = card.querySelector('[data-dirt-n]');
                    const dirtOutput = card.querySelector('[data-dirt-q]');

                    const sampleWeight = (withSampleWeight ?? 0) - (containerWeight ?? 0);
                    const dirtWeight = (crucibleWithDirt ?? 0) - (crucibleEmpty ?? 0);

                    if (sampleOutput) sampleOutput.textContent = formatSamplingNumber(sampleWeight, 4) || '0.00';
                    if (dirtOutput) dirtOutput.textContent = formatSamplingNumber(dirtWeight, 4) || '0.00';

                    let finalValue = null;
                    if (sampleWeight !== 0 && withSampleWeight !== null && containerWeight !== null && crucibleEmpty !== null && crucibleWithDirt !== null) {
                        finalValue = (dirtWeight / sampleWeight) * 100;
                    }
                    resultInput.value = formatSamplingNumber(finalValue, 4);
                }
            });

            // Auto-Sum di Tab Batch
            document.querySelectorAll('#tab-batch [data-total-result]').forEach((totalInput) => {
                const categoryKey = totalInput.dataset.totalCategory || '';
                const categoryCards = document.querySelectorAll(`#tab-batch [data-parameter-card][data-category-key="${categoryKey}"]`);

                let total = 0;
                categoryCards.forEach((card) => {
                    if (card.dataset.isTotal === '1') return;
                    const input = card.querySelector('input[name^="measurements["]');
                    if (!input) return;
                    
                    const numericValue = parseSamplingNumber(input.value);
                    if (numericValue !== null) total += numericValue;
                });
                totalInput.value = formatSamplingNumber(total, 4);
            });
        }

        // ==========================================
        // FUNGSI BARU: DINAMIS UNTUK MODE SATUAN
        // ==========================================
        function handleSatuanSelectChange() {
            const select = document.getElementById('satuan-parameter-select');
            const selectedOption = select.options[select.selectedIndex];
            const category = selectedOption ? selectedOption.getAttribute('data-category-key') : '';

            // Ambil container input
            const genericContainer = document.getElementById('satuan-input-generic');
            const ffaContainer = document.getElementById('satuan-input-ffa');
            const dirtContainer = document.getElementById('satuan-input-dirt');

            // Fungsi untuk enable/disable input agar data tidak bocor saat disubmit
            const toggleContainer = (container, isVisible) => {
                if (isVisible) {
                    container.classList.remove('hidden');
                    container.classList.add('block');
                     // Enable semua input di dalam form ini agar nilai FFA/Dirt ikut terkirim.
                    container.querySelectorAll('input').forEach(input => {
                        input.disabled = false;
                    });
                } else {
                    container.classList.remove('block');
                    container.classList.add('hidden');
                    // Disable semua input yang disembunyikan agar tidak ikut disubmit
                    container.querySelectorAll('input').forEach(input => {
                        input.disabled = true;
                    });
                }
            };

            // Reset UI
            toggleContainer(genericContainer, false);
            toggleContainer(ffaContainer, false);
            toggleContainer(dirtContainer, false);

            if (category === 'ffa') {
                toggleContainer(ffaContainer, true);
                calculateSatuanFfa(); // Kalkulasi ulang jika user edit select dropdown
            } else if (category === 'dirt') {
                toggleContainer(dirtContainer, true);
                calculateSatuanDirt();
            } else {
                toggleContainer(genericContainer, true);
            }
        }

        // Kalkulasi khusus untuk Form Mode Satuan - FFA
        function calculateSatuanFfa() {
            const a = parseSamplingNumber(document.getElementById('satuan_ffa_a').value);
            const b = parseSamplingNumber(document.getElementById('satuan_ffa_b').value);
            const c = parseSamplingNumber(document.getElementById('satuan_ffa_c').value);
            const result = document.getElementById('satuan_ffa_result');

            if (a && a !== 0 && b !== null && c !== null) {
                result.value = formatSamplingNumber((b * c * 25.6) / a, 4);
            } else {
                result.value = '';
            }
        }

        // Kalkulasi khusus untuk Form Mode Satuan - Dirt
        function calculateSatuanDirt() {
            const l = parseSamplingNumber(document.getElementById('satuan_dirt_l').value);
            const m = parseSamplingNumber(document.getElementById('satuan_dirt_m').value);
            const o = parseSamplingNumber(document.getElementById('satuan_dirt_o').value);
            const p = parseSamplingNumber(document.getElementById('satuan_dirt_p').value);
            
            const nDisplay = document.getElementById('satuan_dirt_n');
            const qDisplay = document.getElementById('satuan_dirt_q');
            const result = document.getElementById('satuan_dirt_result');

            const sampleWeight = (m ?? 0) - (l ?? 0);
            const dirtWeight = (p ?? 0) - (o ?? 0);

            nDisplay.textContent = formatSamplingNumber(sampleWeight, 4) || '0.00';
            qDisplay.textContent = formatSamplingNumber(dirtWeight, 4) || '0.00';

            if (sampleWeight !== 0 && l !== null && m !== null && o !== null && p !== null) {
                result.value = formatSamplingNumber((dirtWeight / sampleWeight) * 100, 4);
            } else {
                result.value = '';
            }
        }

        // Misi / Countdown Timer
        function formatCountdown(ms) {
            const totalSeconds = Math.max(0, Math.floor(ms / 1000));
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;
            return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        function initializeMissionCountdowns() {
            const countdownNodes = Array.from(document.querySelectorAll('[data-mission-reset-countdown]'));
            if (countdownNodes.length === 0) return;

            const resetTimes = [];
            const updateCountdowns = () => {
                const now = Date.now();
                countdownNodes.forEach((node) => {
                    const resetAt = new Date(node.dataset.resetAt || '');
                    if (Number.isNaN(resetAt.getTime())) return;

                    const remaining = resetAt.getTime() - now;
                    resetTimes.push(resetAt.getTime());

                    const prefix = node.dataset.missionPrefix || 'Tersedia';
                    if (remaining <= 0) {
                        node.textContent = `${prefix} • reset sekarang`;
                    } else {
                        node.textContent = `${prefix} • reset ${formatCountdown(remaining)}`;
                    }
                });
            };

            updateCountdowns();

            const refreshAt = countdownNodes
                .map((node) => new Date(node.dataset.resetAt || '').getTime())
                .filter((time) => Number.isFinite(time) && time > Date.now())
                .sort((a, b) => a - b)[0];

            if (refreshAt) {
                window.setTimeout(() => { window.location.reload(); }, Math.max(1000, refreshAt - Date.now() + 500));
            }
            window.setInterval(updateCountdowns, 1000);
        }

        // Pemasangan Event Listeners setelah DOM Siap
        document.addEventListener('DOMContentLoaded', function () {
            const satuanForm = document.getElementById('satuan-form');
            const satuanSelect = document.getElementById('satuan-parameter-select');

            calculateSamplingCards();
            initializeMissionCountdowns();

            document.querySelectorAll('#tab-batch [data-ffa-a], #tab-batch [data-ffa-b], #tab-batch [data-ffa-c], #tab-batch [data-dirt-l], #tab-batch [data-dirt-m], #tab-batch [data-dirt-o], #tab-batch [data-dirt-p], #tab-batch input[name^="measurements["]').forEach((input) => {
                input.addEventListener('input', calculateSamplingCards);
            });

            if (satuanSelect) {
                satuanSelect.addEventListener('change', handleSatuanSelectChange);
                handleSatuanSelectChange();
            }

            document.querySelectorAll('#satuan_ffa_a, #satuan_ffa_b, #satuan_ffa_c').forEach((input) => {
                input.addEventListener('input', calculateSatuanFfa);
            });
            document.querySelectorAll('#satuan_dirt_l, #satuan_dirt_m, #satuan_dirt_o, #satuan_dirt_p').forEach((input) => {
                input.addEventListener('input', calculateSatuanDirt);
            });

            if (satuanForm) {
                satuanForm.addEventListener('submit', function () {
                    const timeInput = document.getElementById('hidden_measured_time');
                    if (!timeInput) return;

                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    timeInput.value = `${hours}:${minutes}`;
                });
            }

            document.querySelectorAll('[data-mission-shortcut]').forEach((missionCard) => {
                const missionDone = missionCard.querySelector('[data-mission-done]')?.getAttribute('data-mission-done') === '1';
                if (missionDone) return;

                missionCard.addEventListener('click', function () {
                    const parameterName = missionCard.getAttribute('data-parameter-name') || '';
                    const parameterId = missionCard.getAttribute('data-parameter-id') || '';
                    const paramSelect = document.getElementById('satuan-parameter-select');
                    if (!paramSelect) return;

                    let selectedOption = null;
                    if (parameterId) {
                        selectedOption = Array.from(paramSelect.options).find((option) => option.value === parameterId) || null;
                    }
                    if (!selectedOption && parameterName) {
                        selectedOption = Array.from(paramSelect.options).find((option) => option.textContent.includes(parameterName)) || null;
                    }
                    if (!selectedOption) return;

                    paramSelect.value = selectedOption.value;
                    paramSelect.dispatchEvent(new Event('change', { bubbles: true }));

                    let inputField = null;
                    if (!document.getElementById('satuan-input-ffa').classList.contains('hidden')) {
                        inputField = document.getElementById('satuan_ffa_a');
                    } else if (!document.getElementById('satuan-input-dirt').classList.contains('hidden')) {
                        inputField = document.getElementById('satuan_dirt_l');
                    } else {
                        inputField = document.getElementById('satuan_generic_result');
                    }

                    if (inputField) {
                        inputField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        window.setTimeout(() => inputField.focus(), 250);
                    }
                });
            });
        });
    </script>

    
    <input type="hidden" id="shift-end-state" value="active" data-shift="<?php echo e($activeShift); ?>">
    <input type="hidden" id="shift-mvp-status" value="0">
    <input type="hidden" id="daily-missions-completed" value="<?php echo e(collect($missionGroups ?? [])->get('Target Harian', collect())->filter(fn($m) => $m->mission_completed ?? false)->count()); ?>">
    <input type="hidden" id="daily-missions-total" value="<?php echo e(collect($missionGroups ?? [])->get('Target Harian', collect())->count()); ?>">

    
        <div id="endShiftWarningModal" class="fixed inset-0 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 bg-slate-900/40 backdrop-blur-sm">
            <div class="relative rounded-[32px] border border-white bg-white/95 p-6 sm:p-8 shadow-2xl max-w-md w-full mx-4 backdrop-blur-xl scale-95 transition-transform duration-300 -translate-y-4">
                <button type="button" onclick="closeEndShiftWarning()" class="absolute top-4 right-4 flex h-8 w-8 items-center justify-center rounded-full hover:bg-gray-100">
                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <div class="text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4v2m0 0v2m0-6v-2m0 0V7a2 2 0 012-2h2.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2h-2.586a1 1 0 01-.707-.293l-5.414-5.414a1 1 0 01-.293-.707V7z" /></svg>
                    </div>
                    <h3 id="endShiftWarningTitle" class="text-xl font-extrabold text-gray-900">Konfirmasi End Shift</h3>
                    <p id="endShiftWarningDescription" class="mt-2 text-sm font-medium text-gray-600">
                        Anda akan mengakhiri shift dan mengirim data sampling untuk diverifikasi. Pastikan semua input sudah benar sebelum melanjutkan.
                    </p>
                    <p id="endShiftWarningFooterNote" class="mt-3 text-[10px] font-bold text-red-600 uppercase tracking-wider">
                        Pastikan tidak ada data yang masih perlu diperbaiki.
                    </p>
                    <p class="mt-2 text-sm font-medium text-gray-600 hidden" id="endShiftWarningPenaltyText">
                        Misi Harian adalah tanggung jawab <strong>Shift 1 & Shift 2</strong>. Jika Anda akhiri shift sekarang, kedua shift akan mendapat <strong>PENALTI</strong> dan status misi berubah <strong>FAILED</strong>.
                    </p>
                </div>

                <div class="mt-6 space-y-3">
                    <button type="button" id="endShiftWarningConfirmButton" onclick="confirmEndShift()" class="w-full rounded-[16px] bg-gradient-to-r from-red-500 to-orange-500 px-4 py-3 font-extrabold text-white shadow-lg hover:shadow-xl transition-all active:scale-95">
                        Ya, Akhiri Shift
                    </button>
                    <button type="button" onclick="closeEndShiftWarning()" class="w-full rounded-[16px] border-2 border-gray-200 bg-white px-4 py-3 font-extrabold text-gray-900 hover:bg-gray-50 transition-all">
                        Batalkan
                    </button>
                </div>
            </div>
        </div>

        
        <div id="shiftSummaryReport" class="fixed inset-0 z-40 opacity-0 pointer-events-none transition-opacity duration-500 bg-gradient-to-br from-slate-900/50 via-slate-900/40 to-slate-900/50 backdrop-blur-lg">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="rounded-[40px] border border-white bg-white/95 p-8 sm:p-12 shadow-2xl max-w-2xl w-full backdrop-blur-xl transform transition-all duration-500 scale-95">
                    
                    
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-emerald-400 to-blue-500 mb-4 shadow-lg">
                            <svg class="h-10 w-10 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" /></svg>
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">Shift Complete!</h2>
                        <p class="text-gray-500 font-bold mt-2">Game Clear - Shift Summary Report</p>
                    </div>

                    
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="rounded-[24px] bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 p-4">
                            <p class="text-xs font-extrabold text-blue-600 uppercase tracking-wider">Your Shift</p>
                            <p class="text-2xl font-black text-blue-900 mt-1" id="report-shift-number">1</p>
                        </div>
                        <div class="rounded-[24px] bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 p-4">
                            <p class="text-xs font-extrabold text-purple-600 uppercase tracking-wider">Final Score</p>
                            <p class="text-3xl font-black text-purple-900 mt-1" id="report-final-score">0%</p>
                        </div>
                    </div>

                    
                    <div id="mvpBadgeSection" class="hidden mb-8">
                        <div class="rounded-[24px] bg-gradient-to-r from-amber-300 via-yellow-300 to-orange-300 border-2 border-amber-400 p-6 text-center shadow-lg">
                            <div class="text-4xl mb-2">🏆</div>
                            <p class="text-sm font-black text-amber-950 uppercase tracking-widest">MVP - Penyelesai Misi Harian</p>
                            <p class="text-xs font-bold text-amber-900 mt-1">Anda menyelesaikan Daily Shared Mission!</p>
                        </div>
                    </div>

                    
                    <div class="mb-8">
                        <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-wider mb-4">Complete vs Missed</h3>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between p-3 rounded-[16px] bg-emerald-50 border border-emerald-200">
                                <span class="font-bold text-emerald-900">✓ Completed Missions</span>
                                <span class="text-lg font-black text-emerald-600" id="report-completed-count">0</span>
                            </div>
                            <div class="flex items-center justify-between p-3 rounded-[16px] bg-red-50 border border-red-200">
                                <span class="font-bold text-red-900">✗ Missed Missions</span>
                                <span class="text-lg font-black text-red-600" id="report-missed-count">0</span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="mb-8">
                        <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-wider mb-4">Daily Shared Mission Status</h3>
                        <div class="rounded-[20px] border-2 border-purple-300 bg-purple-50 p-4">
                            <p class="text-sm font-bold text-purple-900" id="report-daily-status">Pending untuk Shift 2</p>
                        </div>
                    </div>

                    
                    <button onclick="exitShiftReport()" class="w-full rounded-[20px] bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 font-extrabold text-white shadow-lg hover:shadow-xl transition-all active:scale-95">
                        Back to Dashboard
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        
        function prepareCloseDailySession() {
            try {
                const dailyCompletedElem = document.getElementById('daily-missions-completed');
                const dailyTotalElem = document.getElementById('daily-missions-total');
                const shiftEndStateElem = document.getElementById('shift-end-state');

                if (!dailyCompletedElem || !dailyTotalElem || !shiftEndStateElem) {
                    console.error('End shift elements not found');
                    alert('Error: System tidak siap. Silakan refresh halaman.');
                    return;
                }

                const dailyCompleted = parseInt(dailyCompletedElem.value) || 0;
                const dailyTotal = parseInt(dailyTotalElem.value) || 0;
                const shift = parseInt(shiftEndStateElem.dataset.shift) || 1;

                console.log('End Shift Check:', { dailyCompleted, dailyTotal, shift });

                const isDailyMissionComplete = dailyTotal > 0 && dailyCompleted >= dailyTotal;
                const isShift2 = shift === 2;

                showEndShiftWarning({
                    isDailyMissionComplete,
                    isShift2,
                });
            } catch (err) {
                console.error('Error in prepareCloseDailySession:', err);
                alert('Ada error saat mempersiapkan tutup sesi harian. Silakan coba lagi.');
            }
        }

        function showEndShiftWarning(context = {}) {
            try {
                const modal = document.getElementById('endShiftWarningModal');
                const title = document.getElementById('endShiftWarningTitle');
                const description = document.getElementById('endShiftWarningDescription');
                const footerNote = document.getElementById('endShiftWarningFooterNote');
                const confirmButton = document.getElementById('endShiftWarningConfirmButton');

                const isDailyMissionComplete = Boolean(context.isDailyMissionComplete);
                const isShift2 = Boolean(context.isShift2);
                const requiresPenalty = isShift2 && !isDailyMissionComplete;

                if (title) {
                    title.textContent = requiresPenalty
                        ? '⚠️ Misi Harian Belum Selesai!'
                        : 'Tutup Sesi Harian';
                }

                if (description) {
                    description.textContent = requiresPenalty
                        ? 'Misi Harian adalah tanggung jawab Shift 1 & Shift 2. Jika Anda akhiri shift sekarang, status misi akan berubah FAILED dan sistem akan mencatat penalty.'
                        : 'Anda akan mengakhiri shift dan mengirim data sampling untuk diverifikasi. Pastikan semua input sudah benar sebelum melanjutkan.';
                }

                if (footerNote) {
                    footerNote.textContent = requiresPenalty
                        ? 'Ini akan mempengaruhi score kedua shift secara permanen.'
                        : 'Pastikan tidak ada data yang masih perlu diperbaiki.';
                }

                if (confirmButton) {
                    confirmButton.textContent = requiresPenalty
                        ? 'Akhiri Shift (Terima Penalty)'
                        : 'Ya, Akhiri Shift';
                    confirmButton.setAttribute('onclick', requiresPenalty ? 'confirmCloseDailySessionWithPenalty()' : 'confirmCloseDailySession()');
                }

                const penaltyText = document.getElementById('endShiftWarningPenaltyText');
                if (penaltyText) {
                    penaltyText.classList.toggle('hidden', !requiresPenalty);
                }

                if (modal) {
                    modal.classList.remove('opacity-0', 'pointer-events-none');
                    modal.classList.add('opacity-100', 'pointer-events-auto');
                } else {
                    console.error('Warning modal not found');
                    alert('Error: Modal tidak ditemukan. Silakan refresh halaman.');
                }
            } catch (err) {
                console.error('Error showing warning:', err);
            }
        }

        function closeCloseDailySessionWarning() {
            try {
                const modal = document.getElementById('endShiftWarningModal');
                if (modal) {
                    modal.classList.add('opacity-0', 'pointer-events-none');
                    modal.classList.remove('opacity-100', 'pointer-events-auto');
                }
            } catch (err) {
                console.error('Error closing warning:', err);
            }
        }

        function confirmCloseDailySessionWithPenalty() {
            try {
                closeCloseDailySessionWarning();
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                
                if (!csrfToken) {
                    throw new Error('CSRF token tidak ditemukan');
                }

                fetch('/lab/sampling/end-shift', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ force_penalty: true }),
                }).then(r => {
                    if (!r.ok) throw new Error('Response status ' + r.status);
                    return r.json();
                }).then(data => {
                    showShiftReport(data);
                }).catch(err => {
                    console.error('Error closing daily session with penalty:', err);
                    alert('Gagal menutup sesi harian. Error: ' + err.message);
                });
            } catch (err) {
                console.error('Error in confirmCloseDailySessionWithPenalty:', err);
                alert('Ada error: ' + err.message);
            }
        }

        function confirmCloseDailySession() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                
                if (!csrfToken) {
                    throw new Error('CSRF token tidak ditemukan');
                }

                fetch('/lab/sampling/end-shift', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ force_penalty: false }),
                }).then(r => {
                    if (!r.ok) throw new Error('Response status ' + r.status);
                    return r.json();
                }).then(data => {
                    showShiftReport(data);
                }).catch(err => {
                    console.error('Error closing daily session:', err);
                    alert('Gagal menutup sesi harian. Error: ' + err.message);
                });
            } catch (err) {
                console.error('Error in confirmCloseDailySession:', err);
                alert('Ada error: ' + err.message);
            }
        }

        function showShiftReport(data) {
            try {
                document.getElementById('shift-end-state').value = 'ended';
                document.getElementById('report-shift-number').textContent = data.shift || 1;
                document.getElementById('report-final-score').textContent = (data.final_score || 0) + '%';
                document.getElementById('report-completed-count').textContent = data.completed_count || 0;
                document.getElementById('report-missed-count').textContent = data.missed_count || 0;
                document.getElementById('report-daily-status').textContent = data.daily_status_text || 'Pending';

                if (data.is_mvp) {
                    document.getElementById('mvpBadgeSection').classList.remove('hidden');
                }

                const formContainer = document.getElementById('form-container');
                const mobileBackdrop = document.getElementById('mobile-backdrop');
                const report = document.getElementById('shiftSummaryReport');
                const reportCard = report?.querySelector('div.rounded-\\[40px\\]');

                if (formContainer) formContainer.style.display = 'none';
                if (mobileBackdrop) mobileBackdrop.style.display = 'none';

                setTimeout(() => {
                    if (report) {
                        report.classList.remove('opacity-0', 'pointer-events-none');
                        report.classList.add('opacity-100', 'pointer-events-auto');
                    }
                    if (reportCard) {
                        reportCard.classList.remove('scale-95');
                        reportCard.classList.add('scale-100');
                    }
                }, 100);
            } catch (err) {
                console.error('Error showing shift report:', err);
                alert('Ada error saat menampilkan laporan. Silakan refresh halaman.');
            }
        }

        function exitShiftReport() {
            window.location.href = "<?php echo e(route('lab.dashboard')); ?>";
        }
    </script>
</body>
</html><?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/LabSystem\resources/views/sampling/form.blade.php ENDPATH**/ ?>