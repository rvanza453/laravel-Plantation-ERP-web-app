

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    .pf-show-wrapper {
        font-family: 'Inter', sans-serif;
        max-width: 1200px;
        margin: 0 auto;
        padding-bottom: 60px;
        color: #111827;
    }

    /* Utilitas Teks */
    .text-muted { color: #6b7280; font-size: 13px; font-weight: 500; }
    .title-md { font-size: 18px; font-weight: 600; letter-spacing: -0.02em; color: #111; margin-bottom: 4px; }

    /* Header & Navigation */
    .pf-header { 
        display: flex; justify-content: space-between; align-items: flex-end; 
        margin-bottom: 32px; gap: 24px; flex-wrap: wrap;
    }
    .pf-meta { 
        font-size: 12px; font-weight: 600; color: #6b7280; 
        display: flex; align-items: center; gap: 8px; margin-bottom: 8px;
    }
    .pf-title { font-size: 36px; font-weight: 700; letter-spacing: -0.03em; color: #111; line-height: 1.1; margin-bottom: 8px; }
    
    /* Layout */
    .pf-main-grid { display: grid; grid-template-columns: 1fr; gap: 20px; }
    @media (min-width: 992px) { .pf-main-grid { grid-template-columns: 1fr 380px; } }

    /* --- CARDS --- */
    .pf-card-light { 
        background: #ffffff; border-radius: 28px; padding: 28px; 
        border: 1px solid #e5e7eb; 
    }
    .pf-card-dark { 
        background: #232220; border-radius: 28px; padding: 28px; 
        color: #ffffff; 
    }
    .pf-card-dark .title-md { color: #ffffff; }
    .pf-card-dark .text-muted { color: #a1a1aa; }

    /* --- INFO GRIDS --- */
    .pf-info-grid { 
        display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 32px; 
        border-bottom: 1px solid #f3f4f6; padding-bottom: 24px;
    }
    @media (min-width: 640px) { .pf-info-grid { grid-template-columns: repeat(3, 1fr); } }
    
    .pf-info-label { font-size: 12px; font-weight: 500; color: #9ca3af; margin-bottom: 4px; }
    .pf-info-value { font-size: 15px; font-weight: 600; color: #111; }
    
    .pf-desc-box { 
        color: #374151; font-size: 14px; line-height: 1.6; white-space: pre-line; 
        margin-bottom: 32px; 
    }

    /* --- ATTACHMENTS --- */
    .pf-attachment-list { display: flex; flex-direction: column; gap: 12px; margin-top: 16px; }
    .pf-attachment-card { 
        border: 1px solid #e5e7eb; border-radius: 20px; padding: 16px 20px; 
        display: flex; align-items: center; justify-content: space-between; gap: 16px; 
    }
    .pf-file-icon { 
        width: 40px; height: 40px; border-radius: 12px; background: #f3f4f6; 
        display: flex; align-items: center; justify-content: center; color: #6b7280; font-size: 18px; 
    }

    /* --- INPUT FORMS (Dark Mode) --- */
    .pf-input-dark { 
        width: 100%; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; 
        padding: 12px 16px; color: #111; font-size: 14px; outline: none; margin-bottom: 16px; 
        font-family: inherit;
    }
    /* Khusus untuk form di dalam Dark Card */
    .pf-card-dark .pf-input-dark {
        background: #33322f; border-color: #4b4a46; color: #ffffff;
    }
    
    /* Token Checkbox */
    .pf-check-group { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; cursor: pointer; }
    .pf-check-box { 
        width: 18px; height: 18px; border-radius: 6px; border: 1px solid #a1a1aa; 
        display: flex; align-items: center; justify-content: center; 
    }
    .pf-check-input:checked + .pf-check-box { background: #ffffff; border-color: #ffffff; }
    .pf-check-input:checked + .pf-check-box i { display: block !important; color: #232220; }

    /* --- TABLES MINIMALIS --- */
    .pf-table-wrapper { width: 100%; overflow-x: auto; margin-top: 16px; }
    .pf-table { width: 100%; border-collapse: collapse; text-align: left; }
    .pf-table th { font-size: 13px; font-weight: 500; color: #9ca3af; padding: 16px 8px 16px 0; border-bottom: 1px solid #e5e7eb; }
    .pf-table td { padding: 16px 8px 16px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; font-weight: 500; color: #111; }
    .pf-table tr:last-child td { border-bottom: none; }

    /* --- BUTTONS & PILLS --- */
    .pf-btn { 
        display: inline-flex; align-items: center; justify-content: center; gap: 8px; 
        padding: 10px 20px; border-radius: 100px; font-size: 14px; font-weight: 600; 
        cursor: pointer; transition: all 0.2s; text-decoration: none; 
    }
    .pf-btn-dark { background: #232220; color: #fff; border: 1px solid #232220; }
    .pf-btn-dark:hover { background: #111; }
    .pf-btn-light { background: #fff; color: #111; border: 1px solid #e5e7eb; }
    .pf-btn-light:hover { background: #f9fafb; }
    .pf-btn-white { background: #ffffff; color: #111; border: 1px solid #ffffff; width: 100%; margin-top: 16px; }
    
    .pf-btn-icon { width: 36px; height: 36px; border-radius: 50%; border: 1px solid #e5e7eb; background: #fff; padding: 0; color: #6b7280; }
    .pf-btn-icon:hover { color: #111; background: #f9fafb; }
    
    /* Status Pill dengan Dot */
    .pf-status-pill { 
        display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; 
        border-radius: 100px; background: #ffffff; border: 1px solid #e5e7eb; 
        font-size: 12px; font-weight: 600; color: #111; text-transform: capitalize; 
    }
    .pf-status-pill::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
    .dot-menunggu::before { background-color: #f59e0b; }
    .dot-diproses::before { background-color: #3b82f6; }
    .dot-selesai::before { background-color: #10b981; }
    .dot-dibatalkan::before { background-color: #ef4444; }

    /* Avatar Dummy */
    .pf-avatar {
        width: 28px; height: 28px; border-radius: 50%;
        background: #e5e7eb; display: inline-flex; align-items: center; justify-content: center;
        font-size: 10px; font-weight: 600; color: #4b5563;
    }
</style>

<div class="pf-show-wrapper">

    
    <?php if(session('success')): ?>
        <div style="background: #ffffff; color: #111; padding: 16px 24px; border-radius: 20px; font-size: 14px; font-weight: 500; margin-bottom: 24px; border: 1px solid #e5e7eb; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-check-circle" style="color: #10b981;"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('share_link')): ?>
        <div style="background: #232220; color: #fff; padding: 28px; border-radius: 28px; margin-bottom: 32px; border: 1px solid #232220;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div class="title-md"><i class="fas fa-link mr-2" style="color: #a1a1aa;"></i> Link Akses Publik Berhasil Dibuat</div>
                <button onclick="navigator.clipboard.writeText('<?php echo e(session('share_link')); ?>'); alert('Link disalin!')" 
                        class="pf-btn pf-btn-white" style="width: auto; margin-top: 0;">
                    Salin Link
                </button>
            </div>
            <div style="background: #33322f; padding: 16px; border-radius: 12px; font-family: monospace; font-size: 14px; color: #e5e7eb; border: 1px solid #4b4a46; word-break: break-all;">
                <?php echo e(session('share_link')); ?>

            </div>
        </div>
    <?php endif; ?>

    
    <div class="pf-header">
        <div>
            <div class="pf-meta">
                <span style="color: #6b7280;">REF #<?php echo e($ticket->nomor_referensi); ?></span> 
                <span style="color: #d1d5db;">•</span>
                <span class="pf-status-pill dot-<?php echo e($ticket->status_proses); ?>" style="padding: 2px 10px; font-size: 11px; border:none; background: #f3f4f6;"><?php echo e($ticket->status_proses); ?></span>
            </div>
            <h1 class="pf-title"><?php echo e($ticket->judul_permintaan ?: 'Tiket Permintaan Data'); ?></h1>
            <div style="font-size: 14px; font-weight: 500; color: #6b7280; display: flex; align-items: center; gap: 8px;">
                <div class="pf-avatar"><?php echo e(strtoupper(substr(str_replace('_', ' ', $ticket->pihak_peminta), 0, 1))); ?></div>
                <span><?php echo e(ucwords(str_replace('_', ' ', $ticket->pihak_peminta))); ?></span>
            </div>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="<?php echo e(route('hr.external-requests.index')); ?>" class="pf-btn pf-btn-light">
                Kembali
            </a>
            <a href="<?php echo e(route('hr.external-requests.edit', $ticket)); ?>" class="pf-btn pf-btn-dark">
                Edit Tiket
            </a>
        </div>
    </div>

    <div class="pf-main-grid">
        
        
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            
            <div class="pf-card-light">
                <div class="title-md" style="margin-bottom: 24px;">Rincian Tiket</div>

                <div class="pf-info-grid">
                    <div>
                        <div class="pf-info-label">Tanggal Masuk</div>
                        <div class="pf-info-value"><?php echo e(optional($ticket->tanggal_surat_masuk)->format('d F Y')); ?></div>
                    </div>
                    <div>
                        <div class="pf-info-label">Kategori Data</div>
                        <div class="pf-info-value" style="text-transform: capitalize;"><?php echo e(str_replace('_', ' ', $ticket->kategori_data)); ?></div>
                    </div>
                    <div>
                        <div class="pf-info-label">PIC Internal</div>
                        <div class="pf-info-value"><?php echo e($ticket->picUser->name ?? '-'); ?></div>
                    </div>
                </div>

                <div class="pf-info-label">Deskripsi Permintaan</div>
                <div class="pf-desc-box">
                    <?php echo e($ticket->deskripsi_permintaan); ?>

                </div>

                <?php if($ticket->catatan_internal): ?>
                    <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 20px; padding: 20px;">
                        <div class="pf-info-label" style="color: #111; font-weight: 600;"><i class="fas fa-lock mr-1" style="color: #9ca3af;"></i> Catatan Internal (Hanya Admin)</div>
                        <div style="color: #4b5563; font-size: 13px; font-style: italic; margin-top: 8px;">
                            "<?php echo e($ticket->catatan_internal); ?>"
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="pf-card-light">
                <div class="title-md">Lampiran File</div>
                <div class="text-muted">Berkas yang diunggah untuk tiket ini (<?php echo e($ticket->attachments->count()); ?>)</div>
                
                <div class="pf-attachment-list">
                    <?php $__empty_1 = true; $__currentLoopData = $ticket->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="pf-attachment-card">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div class="pf-file-icon"><i class="fas fa-file-alt"></i></div>
                                <div>
                                    <div style="font-size: 14px; font-weight: 500; color: #111;"><?php echo e($file->file_name); ?></div>
                                    <div style="font-size: 12px; color: #9ca3af;"><?php echo e(ucwords(str_replace('_', ' ', $file->kategori_lampiran))); ?> • <?php echo e(number_format(($file->file_size ?? 0)/1024, 1)); ?> KB</div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <a href="<?php echo e(route('hr.external-requests.attachments.preview', [$ticket, $file])); ?>" target="_blank" class="pf-btn-icon pf-btn"><i class="fas fa-eye text-xs"></i></a>
                                <a href="<?php echo e(route('hr.external-requests.attachments.download', [$ticket, $file])); ?>" class="pf-btn-icon pf-btn"><i class="fas fa-download text-xs"></i></a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div style="padding: 32px 0; text-align: center; color: #9ca3af;">
                            <p style="font-size: 13px; font-weight: 500;">Tidak ada berkas terlampir.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="pf-card-light">
                <div class="title-md">Riwayat Akses Publik</div>
                <div class="text-muted">Daftar link yang pernah dibuat untuk tiket ini</div>
                
                <div class="pf-table-wrapper">
                    <table class="pf-table">
                        <thead>
                            <tr>
                                <th>Token Hint</th>
                                <th>Policy</th>
                                <th>Views</th>
                                <th>Exp. At</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $ticket->shareTokens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $token): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr style="<?php echo e($token->revoked_at ? 'opacity: 0.4;' : ''); ?>">
                                    <td style="font-family: monospace; font-size: 14px; color: #111;"><?php echo e($token->token_hint); ?>...</td>
                                    <td>
                                        <div style="display: flex; gap: 4px;">
                                            <?php if($token->allow_download): ?> <span style="font-size: 10px; font-weight: 600; padding: 2px 8px; background: #f3f4f6; color: #111; border-radius: 100px;">Download</span> <?php endif; ?>
                                            <?php if($token->allow_preview_only): ?> <span style="font-size: 10px; font-weight: 600; padding: 2px 8px; background: #f3f4f6; color: #111; border-radius: 100px;">Preview Only</span> <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="color: #6b7280;"><?php echo e($token->view_count); ?> / <?php echo e($token->max_views ?: '∞'); ?></td>
                                    <td style="color: #6b7280; font-size: 13px;"><?php echo e(optional($token->expires_at)->format('d M y, H:i') ?: '-'); ?></td>
                                    <td style="text-align: right;">
                                        <?php if(!$token->revoked_at): ?>
                                            <form method="POST" action="<?php echo e(route('hr.external-requests.share.revoke', [$ticket, $token])); ?>" onsubmit="return confirm('Revoke link ini?')">
                                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                                <button type="submit" style="background: transparent; border: none; color: #ef4444; font-size: 13px; font-weight: 500; cursor: pointer;">Revoke</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="font-size: 13px; font-weight: 500; color: #9ca3af;">Revoked</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 32px 0; color: #9ca3af; font-size: 13px;">Belum ada akses publik dibuat.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            
            <div class="pf-card-light">
                <div class="title-md">Progress Tiket</div>
                
                <div style="margin-top: 24px;">
                    <div class="pf-info-label">Batas Waktu (Deadline)</div>
                    <div style="font-size: 20px; font-weight: 600; color: #111; letter-spacing: -0.02em;">
                        <?php echo e(optional($ticket->deadline)->format('d M Y') ?: '-'); ?>

                    </div>
                    <?php if(optional($ticket->deadline)->isPast() && $ticket->status_proses !== 'selesai'): ?>
                        <div style="font-size: 12px; font-weight: 500; color: #ef4444; margin-top: 4px;">Melewati Deadline</div>
                    <?php endif; ?>
                </div>

                <?php if($ticket->tanggal_selesai): ?>
                    <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #f3f4f6;">
                        <div class="pf-info-label">Tanggal Diselesaikan</div>
                        <div style="font-size: 16px; font-weight: 600; color: #111;">
                            <?php echo e($ticket->tanggal_selesai->format('d M Y')); ?>

                        </div>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="pf-card-dark">
                <div class="title-md">Share Tautan</div>
                <div class="text-muted" style="margin-bottom: 24px;">Beri akses eksternal sementara tanpa login.</div>
                
                <form method="POST" action="<?php echo e(route('hr.external-requests.share.generate', $ticket)); ?>">
                    <?php echo csrf_field(); ?>
                    
                    <label class="pf-info-label" style="color: #a1a1aa; display: block;">Masa Berlaku</label>
                    <input type="datetime-local" name="expires_at" class="pf-input-dark">

                    <label class="pf-info-label" style="color: #a1a1aa; display: block;">Max Klik (Kosongkan = ∞)</label>
                    <input type="number" name="max_views" class="pf-input-dark" placeholder="Contoh: 5">

                    <div style="margin-top: 20px;">
                        <label class="pf-check-group">
                            <div style="position: relative;">
                                <input type="checkbox" name="allow_download" value="1" checked class="pf-check-input" style="opacity: 0; position: absolute; cursor: pointer;">
                                <div class="pf-check-box"><i class="fas fa-check text-[10px] text-white" style="display: none;"></i></div>
                            </div>
                            <span style="font-size: 13px; font-weight: 500; color: #d1d5db;">Izinkan Unduh File</span>
                        </label>
                        <label class="pf-check-group">
                            <div style="position: relative;">
                                <input type="checkbox" name="allow_preview_only" value="1" class="pf-check-input" style="opacity: 0; position: absolute; cursor: pointer;">
                                <div class="pf-check-box"><i class="fas fa-check text-[10px] text-white" style="display: none;"></i></div>
                            </div>
                            <span style="font-size: 13px; font-weight: 500; color: #d1d5db;">Hanya Pratinjau</span>
                        </label>
                    </div>

                    <button type="submit" class="pf-btn pf-btn-white">
                        Generate Tautan
                    </button>
                </form>
            </div>

        </div>

    </div>
</div>

<script>
    // Script sederhana untuk custom checkbox toggle UI di form "Share Tautan"
    document.querySelectorAll('.pf-check-input').forEach(input => {
        input.addEventListener('change', function() {
            const box = this.closest('.pf-check-group').querySelector('.pf-check-box');
            const icon = box.querySelector('i');
            if(this.checked) {
                box.style.background = '#ffffff';
                box.style.borderColor = '#ffffff';
                icon.style.display = 'block';
            } else {
                box.style.background = 'transparent';
                box.style.borderColor = '#a1a1aa';
                icon.style.display = 'none';
            }
        });
        // Inisialisasi status awal
        if(input.checked) input.dispatchEvent(new Event('change'));
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/SystemISPO\resources/views/hr/external-requests/show.blade.php ENDPATH**/ ?>