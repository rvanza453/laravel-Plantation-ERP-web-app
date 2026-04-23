<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .title { text-align: center; font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .subtitle { text-align: center; font-size: 11px; color: #4b5563; margin-bottom: 18px; }
        .section { margin-bottom: 14px; }
        .section h3 { font-size: 12px; margin: 0 0 6px; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; vertical-align: top; }
        th { background: #f3f4f6; text-align: left; }
        .label { width: 180px; font-weight: 700; background: #f9fafb; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <div class="title">DRAFT DOKUMEN SPK</div>
    <div class="subtitle">Service Agreement System - Untuk Review Legal</div>

    <div class="section">
        <h3>Informasi USPK</h3>
        <table>
            <tr>
                <td class="label">Nomor USPK</td>
                <td><?php echo e($uspk->uspk_number); ?></td>
            </tr>
            <tr>
                <td class="label">Judul Pekerjaan</td>
                <td><?php echo e($uspk->title); ?></td>
            </tr>
            <tr>
                <td class="label">Department / Afdeling</td>
                <td><?php echo e($uspk->department->name ?? '-'); ?> / <?php echo e($uspk->subDepartment->name ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="label">Aktivitas</td>
                <td><?php echo e($uspk->job->name ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="label">Pengaju</td>
                <td><?php echo e($uspk->submitter->name ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="label">Estimasi Awal</td>
                <td>Rp <?php echo e(number_format((float) $uspk->estimated_value, 0, ',', '.')); ?> <?php if($uspk->estimated_duration): ?> / <?php echo e($uspk->estimated_duration); ?> hari <?php endif; ?></td>
            </tr>
            <tr>
                <td class="label">Deskripsi</td>
                <td><?php echo e($uspk->description ?: '-'); ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Pemenang Kontraktor (Keputusan Approver Final)</h3>
        <?php if($uspk->selectedTender): ?>
            <table>
                <tr>
                    <td class="label">Nama Kontraktor</td>
                    <td><?php echo e($uspk->selectedTender->contractor->name ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Perusahaan</td>
                    <td><?php echo e($uspk->selectedTender->contractor->company_name ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Nilai Nego Final</td>
                    <td>Rp <?php echo e(number_format((float) $uspk->selectedTender->tender_value, 0, ',', '.')); ?></td>
                </tr>
                <tr>
                    <td class="label">Durasi Final</td>
                    <td><?php echo e($uspk->selectedTender->tender_duration ? $uspk->selectedTender->tender_duration . ' hari' : '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Catatan Nego</td>
                    <td><?php echo e($uspk->selectedTender->description ?: '-'); ?></td>
                </tr>
            </table>
        <?php else: ?>
            <p class="muted">Belum ada pemenang final.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h3>Lampiran Tender Pembanding</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kontraktor</th>
                    <th>Nilai</th>
                    <th>Durasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $uspk->tenders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $tender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($idx + 1); ?></td>
                    <td><?php echo e($tender->contractor->name ?? '-'); ?></td>
                    <td>Rp <?php echo e(number_format((float) $tender->tender_value, 0, ',', '.')); ?></td>
                    <td><?php echo e($tender->tender_duration ? $tender->tender_duration . ' hari' : '-'); ?></td>
                    <td><?php echo e($tender->is_selected ? 'Pemenang Final' : 'Pembanding'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="muted">Tidak ada data tender.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/ServiceAgreementSystem\resources/views/uspk/pdf/spk-draft.blade.php ENDPATH**/ ?>