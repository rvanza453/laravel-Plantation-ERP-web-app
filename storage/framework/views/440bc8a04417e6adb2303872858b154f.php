

<?php $__env->startSection('content'); ?>
<!-- FontAwesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    .payflow-container {
        font-family: 'Inter', sans-serif;
        max-width: 1200px;
        margin: 0 auto;
        padding-bottom: 40px;
        color: #111827;
    }

    /* Page Header */
    .pf-page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }
    .pf-page-title { font-size: 24px; font-weight: 700; color: #111; letter-spacing: -0.02em; }
    .pf-page-subtitle { color: #6b7280; font-size: 14px; font-weight: 500; margin-top: 4px; }

    /* Filter Bar */
    .pf-filter-bar {
        background: #ffffff;
        padding: 12px 12px 12px 24px;
        border-radius: 100px;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    .pf-search-wrapper { flex: 1; position: relative; }
    .pf-search-icon { position: absolute; left: 0; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 14px; }
    .pf-search-input { width: 100%; border: none; background: transparent; padding: 8px 8px 8px 24px; font-size: 14px; outline: none; font-family: inherit; }
    
    .pf-select-minimal { border: none; background: transparent; color: #4b5563; font-size: 13px; font-weight: 600; outline: none; cursor: pointer; padding-right: 20px; }

    /* Table Container */
    .pf-card-light {
        background: #ffffff;
        border-radius: 32px;
        padding: 32px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }

    .pf-table { width: 100%; border-collapse: collapse; }
    .pf-table th {
        font-size: 12px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;
        padding: 16px 20px; border-bottom: 1px solid #f3f4f6; text-align: left;
    }
    .pf-table td { padding: 20px; border-bottom: 1px solid #f9fafb; font-size: 14px; color: #111; vertical-align: middle; }
    .pf-table tr:last-child td { border-bottom: none; }
    .pf-table tr:hover td { background: #fafafa; }

    /* Buttons */
    .pf-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 12px 24px; border-radius: 100px; font-size: 14px; font-weight: 600;
        cursor: pointer; transition: all 0.2s ease; text-decoration: none; border: 1px solid transparent;
    }
    .pf-btn-dark { background: #232220; color: #fff; }
    .pf-btn-dark:hover { background: #333; transform: translateY(-1px); }
    
    .pf-btn-icon { width: 40px; height: 40px; border-radius: 50%; background: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; padding: 0; }
    .pf-btn-icon:hover { background: #e5e7eb; color: #111; }

    /* Badge & Status */
    .pf-status-pill {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 6px 14px; border-radius: 100px;
        background: #fff; border: 1px solid #e5e7eb;
        font-size: 12px; font-weight: 600; color: #111;
        text-transform: capitalize;
    }
    .pf-status-pill::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
    .dot-menunggu::before { background-color: #f59e0b; }
    .dot-diproses::before { background-color: #3b82f6; }
    .dot-selesai::before { background-color: #10b981; }
    .dot-dibatalkan::before { background-color: #ef4444; }

    .pf-avatar {
        width: 38px; height: 38px; border-radius: 50%;
        background: #f3f4f6; display: inline-flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; color: #4b5563; border: 1px solid #e5e7eb;
    }
</style>

<div class="payflow-container">
    
    <div class="pf-page-header">
        <div>
            <h1 class="pf-page-title">Permintaan Data Eksternal</h1>
            <p class="pf-page-subtitle">Pusat distribusi dan audit trail pertukaran data keluar.</p>
        </div>
        <a href="<?php echo e(route('hr.external-requests.create')); ?>" class="pf-btn pf-btn-dark">
            <i class="fas fa-plus"></i> Registrasi Tiket
        </a>
    </div>

    <?php if(session('success')): ?>
        <div style="background: #ecfdf5; color: #065f46; padding: 16px 24px; border-radius: 20px; font-size: 14px; font-weight: 600; margin-bottom: 24px; border: 1px solid #d1fae5; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <form method="GET" action="<?php echo e(route('hr.external-requests.index')); ?>" class="pf-filter-bar shadow-sm">
        <div class="pf-search-wrapper">
            <i class="fas fa-search pf-search-icon"></i>
            <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Cari nomor referensi atau pihak peminta..." class="pf-search-input">
        </div>
        
        <div style="display: flex; align-items: center; border-left: 1px solid #e5e7eb; padding-left: 16px; gap: 12px;">
            <select name="status" class="pf-select-minimal" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($status); ?>" <?php if(request('status') === $status): echo 'selected'; endif; ?>><?php echo e(ucfirst($status)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" class="pf-btn pf-btn-dark" style="padding: 8px 20px; font-size: 12px;">Filter</button>
            <a href="<?php echo e(route('hr.external-requests.index')); ?>" class="pf-btn-icon pf-btn flex items-center justify-center" title="Reset"><i class="fas fa-sync-alt text-xs"></i></a>
        </div>
    </form>

    
    <div class="pf-card-light">
        <div class="overflow-x-auto">
            <table class="pf-table">
                <thead>
                    <tr>
                        <th>Pihak Peminta</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th style="text-align: right;">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="pf-avatar"><?php echo e(strtoupper(substr(str_replace('_', ' ', $ticket->pihak_peminta), 0, 1))); ?></div>
                                    <div>
                                        <div style="font-weight: 600; color: #111;"><?php echo e(ucwords(str_replace('_', ' ', $ticket->pihak_peminta))); ?></div>
                                        <div style="font-size: 11px; color: #9ca3af; font-weight: 500; font-family: monospace;">#<?php echo e($ticket->nomor_referensi); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="color: #6b7280; font-weight: 500;"><?php echo e(ucwords(str_replace('_', ' ', $ticket->kategori_data))); ?></span>
                            </td>
                            <td>
                                <span class="pf-status-pill dot-<?php echo e($ticket->status_proses); ?>"><?php echo e($ticket->status_proses); ?></span>
                            </td>
                            <td>
                                <?php $isLate = optional($ticket->deadline)->isPast() && $ticket->status_proses !== 'selesai'; ?>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 600; color: <?php echo e($isLate ? '#ef4444' : '#4b5563'); ?>;">
                                        <?php echo e(optional($ticket->deadline)->format('d M Y')); ?>

                                    </span>
                                    <?php if($isLate): ?>
                                        <span style="font-size: 9px; color: #ef4444; font-weight: 700; text-transform: uppercase;">Overdue</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?php echo e(route('hr.external-requests.show', $ticket)); ?>" class="pf-btn pf-btn-icon">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 64px 0; color: #9ca3af;">
                                <div style="margin-bottom: 16px;"><i class="fas fa-folder-open fa-3x" style="opacity: 0.3;"></i></div>
                                <div style="font-weight: 600;">Tidak ada data ditemukan</div>
                                <p style="font-size: 13px; margin-top: 4px;">Atur filter atau kata kunci pencarian Anda.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($tickets->hasPages()): ?>
            <div style="margin-top: 32px;">
                <?php echo e($tickets->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/SystemISPO\resources/views/hr/external-requests/index.blade.php ENDPATH**/ ?>