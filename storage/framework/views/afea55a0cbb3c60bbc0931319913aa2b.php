<?php if (isset($component)) { $__componentOriginal8ffca6aaed06613cf5643e13e74c8806 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806 = $attributes; } ?>
<?php $component = Modules\ServiceAgreementSystem\View\Components\Layouts\Master::resolve(['title' => 'Dashboard'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('serviceagreementsystem::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Modules\ServiceAgreementSystem\View\Components\Layouts\Master::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('styles'); ?>
    <style>
        /* Premium Dashboard Overrides */
        .sas-dashboard {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .welcome-banner {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            border-radius: 16px;
            padding: 32px;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
            margin-bottom: 32px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            z-index: 1;
        }

        .welcome-content {
            position: relative;
            z-index: 2;
        }

        .welcome-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .welcome-subtitle {
            font-size: 15px;
            color: rgba(255,255,255,0.85);
            font-weight: 400;
        }

        .welcome-action {
            z-index: 2;
        }

        .btn-white {
            background: white;
            color: #1e3a8a;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        .btn-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            color: #3b82f6;
        }

        /* Enhanced Stats Grid */
        .premium-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .premium-stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .premium-stat-card:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: rgba(59, 130, 246, 0.3);
        }

        .premium-icon-box {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }

        .premium-stat-card:hover .premium-icon-box {
            transform: scale(1.1) rotate(5deg);
        }

        .premium-stat-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .premium-stat-value {
            font-size: 28px;
            font-weight: 800;
            line-height: 1;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .premium-stat-label {
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Specific Color Variations */
        .card-total {
            background: linear-gradient(135deg, white, #f8fafc);
        }
        .icon-total { background: #eff6ff; color: #3b82f6; }
        
        .card-draft {
            background: linear-gradient(135deg, white, #f1f5f9);
        }
        .icon-draft { background: #f1f5f9; color: #64748b; }
        
        .card-submitted {
            background: linear-gradient(135deg, white, #fffbeb);
        }
        .icon-submitted { background: #fffbeb; color: #d97706; }
        
        .card-review {
            background: linear-gradient(135deg, white, #f5f3ff);
        }
        .icon-review { background: #f5f3ff; color: #7c3aed; }

        .card-approved {
            background: linear-gradient(135deg, white, #ecfdf5);
        }
        .icon-approved { background: #ecfdf5; color: #059669; }

        .card-rejected {
            background: linear-gradient(135deg, white, #fef2f2);
        }
        .icon-rejected { background: #fef2f2; color: #dc2626; }

        /* Enhanced Table Card */
        .premium-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(226, 232, 240, 0.8);
            overflow: hidden;
        }

        .premium-card-header {
            padding: 24px 32px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fafafa;
        }

        .premium-card-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .premium-card-title i {
            color: #3b82f6;
            background: #eff6ff;
            padding: 8px;
            border-radius: 8px;
            font-size: 16px;
        }

        .premium-table-wrapper table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .premium-table-wrapper th {
            background: #f8fafc;
            padding: 16px 24px;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }

        .premium-table-wrapper td {
            padding: 20px 24px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 14px;
            transition: background 0.2s;
        }

        .premium-table-wrapper tbody tr:hover td {
            background: #f8fafc;
        }

        .premium-table-wrapper tbody tr:last-child td {
            border-bottom: none;
        }

        .uspk-identifier {
            font-weight: 700;
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .uspk-identifier:hover {
            color: #3b82f6;
        }

        /* Modern Badges */
        .modern-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 600;
            position: relative;
        }

        .modern-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .mb-draft { background: #f1f5f9; color: #475569; }
        .mb-draft::before { background: #64748b; }
        
        .mb-submitted { background: #fffbeb; color: #b45309; }
        .mb-submitted::before { background: #d97706; }
        
        .mb-in_review { background: #f5f3ff; color: #6d28d9; }
        .mb-in_review::before { background: #7c3aed; }
        
        .mb-approved { background: #ecfdf5; color: #047857; }
        .mb-approved::before { background: #059669; }
        
        .mb-rejected { background: #fef2f2; color: #b91c1c; }
        .mb-rejected::before { background: #dc2626; }

        /* Modern Empty State */
        .modern-empty {
            padding: 60px 20px;
            text-align: center;
        }
        
        .modern-empty-icon {
            font-size: 64px;
            color: #cbd5e1;
            margin-bottom: 24px;
        }
        
        .modern-empty h4 {
            color: #0f172a;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .modern-empty p {
            color: #64748b;
            font-size: 14px;
        }

        /* Charts Layout */
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 32px;
        }

        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .chart-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(226, 232, 240, 0.8);
            padding: 24px;
            transition: all 0.3s ease;
        }
        
        .chart-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .chart-header {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .chart-header i {
            font-size: 18px;
            padding: 8px;
            border-radius: 8px;
            background: #f8fafc;
        }

    </style>
    <?php $__env->stopPush(); ?>

    <div class="sas-dashboard">
        
        <div class="welcome-banner">
            <div class="welcome-content">
                <h2 class="welcome-title">Halo, <?php echo e(auth()->user()->name); ?>! 👋</h2>
                <p class="welcome-subtitle">Berikut adalah ringkasan status Service Agreement System (SAS) hari ini.</p>
            </div>
            <?php if(in_array(auth()->user()->role?->name, ['Staff', 'Admin'])): ?>
            <div class="welcome-action">
                <a href="<?php echo e(route('sas.uspk.create')); ?>" class="btn-white">
                    <i class="fas fa-plus"></i> Buat USPK Baru
                </a>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="premium-stats-grid">
            <div class="premium-stat-card card-total">
                <div class="premium-icon-box icon-total">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="premium-stat-info">
                    <span class="premium-stat-value"><?php echo e($stats['total_uspk']); ?></span>
                    <span class="premium-stat-label">Total USPK</span>
                </div>
            </div>

            <div class="premium-stat-card card-draft">
                <div class="premium-icon-box icon-draft">
                    <i class="fas fa-pencil-alt"></i>
                </div>
                <div class="premium-stat-info">
                    <span class="premium-stat-value"><?php echo e($stats['draft']); ?></span>
                    <span class="premium-stat-label">Draft</span>
                </div>
            </div>

            <div class="premium-stat-card card-submitted">
                <div class="premium-icon-box icon-submitted">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="premium-stat-info">
                    <span class="premium-stat-value"><?php echo e($stats['submitted']); ?></span>
                    <span class="premium-stat-label">Submitted</span>
                </div>
            </div>

            <div class="premium-stat-card card-review">
                <div class="premium-icon-box icon-review">
                    <i class="fas fa-search"></i>
                </div>
                <div class="premium-stat-info">
                    <span class="premium-stat-value"><?php echo e($stats['in_review']); ?></span>
                    <span class="premium-stat-label">In Review</span>
                </div>
            </div>

            <div class="premium-stat-card card-approved">
                <div class="premium-icon-box icon-approved">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="premium-stat-info">
                    <span class="premium-stat-value"><?php echo e($stats['approved']); ?></span>
                    <span class="premium-stat-label">Approved</span>
                </div>
            </div>

            <div class="premium-stat-card card-rejected">
                <div class="premium-icon-box icon-rejected">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="premium-stat-info">
                    <span class="premium-stat-value"><?php echo e($stats['rejected']); ?></span>
                    <span class="premium-stat-label">Rejected</span>
                </div>
            </div>
        </div>

        
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <i class="fas fa-building" style="color: #3b82f6; background: #eff6ff;"></i> 
                    Jumlah USPK per Afdeling
                </div>
                <div id="chart-department"></div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header">
                    <i class="fas fa-chart-pie" style="color: #8b5cf6; background: #f5f3ff;"></i> 
                    Persentase Status USPK
                </div>
                <div id="chart-status" style="display:flex; justify-content:center;"></div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <i class="fas fa-trophy" style="color: #f59e0b; background: #fffbeb;"></i> 
                    Top 5 Kontraktor Terpilih
                </div>
                <div id="chart-top-contractors"></div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <i class="fas fa-exclamation-triangle" style="color: #ef4444; background: #fef2f2;"></i> 
                    Rapor Merah: Sering Melewati Tenggat
                </div>
                <div id="chart-late-contractors"></div>
            </div>
        </div>

        
        <div class="premium-card">
            <div class="premium-card-header">
                <div class="premium-card-title">
                    <i class="fas fa-clock"></i> USPK Terbaru
                </div>
                <a href="<?php echo e(route('sas.uspk.index')); ?>" class="btn btn-primary" style="border-radius: 10px;">
                    Lihat Semua <i class="fas fa-arrow-right" style="margin-left: 4px;"></i>
                </a>
            </div>
            <div class="premium-table-wrapper" style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>No. USPK</th>
                            <th>Judul Kegiatan</th>
                            <th>Department</th>
                            <th>Pengaju</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentUspk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uspk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(route('sas.uspk.show', $uspk->id)); ?>" style="text-decoration: none;">
                                    <span class="uspk-identifier">
                                        <i class="fas fa-file-contract" style="color: #64748b;"></i>
                                        <?php echo e($uspk->uspk_number); ?>

                                    </span>
                                </a>
                            </td>
                            <td style="font-weight: 500; color: #1e293b;"><?php echo e($uspk->title); ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-building" style="color: #94a3b8;"></i>
                                    <?php echo e($uspk->department->name ?? '-'); ?>

                                </div>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 24px; height: 24px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; color: #475569;">
                                        <?php echo e(substr($uspk->submitter->name ?? '?', 0, 2)); ?>

                                    </div>
                                    <?php echo e($uspk->submitter->name ?? '-'); ?>

                                </div>
                            </td>
                            <td>
                                <span class="modern-badge mb-<?php echo e(strtolower($uspk->status)); ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $uspk->status))); ?>

                                </span>
                            </td>
                            <td style="color: #64748b;"><?php echo e($uspk->created_at->format('d M Y')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6">
                                <div class="modern-empty">
                                    <div class="modern-empty-icon">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <h4>Belum Ada Pengajuan</h4>
                                    <p>Tarik napas panjang, belum ada aktivitas USPK sejauh ini.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Define Empty State Helper
            const emptyHTML = '<div class="modern-empty" style="padding:30px;"><i class="fas fa-inbox modern-empty-icon" style="font-size:32px;margin-bottom:10px;"></i><p>Belum ada data</p></div>';

            // 1. Chart: Department
            const deptData = <?php echo json_encode($uspkByDepartment, 15, 512) ?>;
            if(deptData.length > 0) {
                new ApexCharts(document.querySelector("#chart-department"), {
                    series: [{ name: 'Total USPK', data: deptData.map(d => d.total) }],
                    chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Manrope, sans-serif' },
                    plotOptions: { bar: { borderRadius: 6, horizontal: false, columnWidth: '45%' } },
                    colors: ['#3b82f6'],
                    dataLabels: { enabled: false },
                    xaxis: { categories: deptData.map(d => d.name), axisBorder: {show:false}, axisTicks:{show:false} },
                    yaxis: { labels: { formatter: (val) => Math.floor(val) } },
                    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
                }).render();
            } else {
                document.querySelector("#chart-department").innerHTML = emptyHTML;
            }

            // 2. Chart: Status
            const statusData = <?php echo json_encode($mappedStatusData, 15, 512) ?>;
            if(statusData.length > 0) {
                // Determine colors based on labels logically
                const colorMap = { 'Draft': '#64748b', 'Submitted': '#d97706', 'In Review': '#7c3aed', 'Approved': '#059669', 'Rejected': '#dc2626' };
                const donutColors = statusData.map(d => colorMap[d.name] || '#3b82f6');
                
                new ApexCharts(document.querySelector("#chart-status"), {
                    series: statusData.map(d => d.total),
                    chart: { type: 'donut', height: 300, fontFamily: 'Manrope, sans-serif' },
                    labels: statusData.map(d => d.name),
                    colors: donutColors,
                    plotOptions: { 
                        pie: { donut: { size: '65%', labels: { show: true, name: {show: true}, value: {show: true, formatter:(v)=>v} } } } 
                    },
                    dataLabels: { enabled: false },
                    stroke: { width: 0 },
                    legend: { position: 'bottom' }
                }).render();
            } else {
                document.querySelector("#chart-status").innerHTML = emptyHTML;
            }

            // 3. Chart: Top Contractors
            const topCData = <?php echo json_encode($topContractors, 15, 512) ?>;
            if(topCData.length > 0) {
                new ApexCharts(document.querySelector("#chart-top-contractors"), {
                    series: [{ name: 'Proyek Terpilih', data: topCData.map(d => d.total) }],
                    chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Manrope, sans-serif' },
                    plotOptions: { bar: { borderRadius: 6, horizontal: true, distributed: true } },
                    colors: ['#f59e0b', '#fbbf24', '#fcd34d', '#fde68a', '#fef3c7'],
                    dataLabels: { enabled: true, style: { colors: ['#fff', '#fff', '#000', '#000', '#000'] } },
                    xaxis: { categories: topCData.map(d => d.name), labels: { formatter: (val) => Math.floor(val) } },
                    grid: { show: false },
                    legend: { show: false }
                }).render();
            } else {
                document.querySelector("#chart-top-contractors").innerHTML = emptyHTML;
            }

            // 4. Chart: Late Contractors
            const lateCData = <?php echo json_encode($lateContractors, 15, 512) ?>;
            if(lateCData.length > 0) {
                new ApexCharts(document.querySelector("#chart-late-contractors"), {
                    series: [{ name: 'Kali Terlambat', data: lateCData.map(d => d.total) }],
                    chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Manrope, sans-serif' },
                    plotOptions: { bar: { borderRadius: 6, horizontal: true, barHeight: '50%' } },
                    colors: ['#ef4444'],
                    dataLabels: { enabled: true, style: { colors: ['#fff'] } },
                    xaxis: { categories: lateCData.map(d => d.name), labels: { formatter: (val) => Math.floor(val) } },
                    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
                }).render();
            } else {
                document.querySelector("#chart-late-contractors").innerHTML = '<div class="modern-empty" style="padding:30px;"><i class="fas fa-check-circle modern-empty-icon" style="font-size:32px;margin-bottom:10px;color:#10b981;"></i><p>Luar biasa! Tidak ada histori kontraktor terlambat.</p></div>';
            }
        });
    </script>
    <?php $__env->stopPush(); ?>
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
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/ServiceAgreementSystem\resources/views/dashboard.blade.php ENDPATH**/ ?>