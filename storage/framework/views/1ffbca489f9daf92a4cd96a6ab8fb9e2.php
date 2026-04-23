<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow,noarchive" />
    <title>Akses Dokumen - <?php echo e($ticket->nomor_referensi); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        pf: {
                            bg: '#f3f3f2',
                            dark: '#232220',
                            darker: '#111111',
                            darkborder: '#4b4a46',
                            light: '#ffffff',
                            border: '#e5e7eb',
                            muted: '#6b7280',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-pf-bg text-gray-900 min-h-screen font-sans antialiased py-12 px-4 sm:px-6">
    
    <main class="max-w-3xl mx-auto space-y-6">
        
        
        <section class="bg-pf-dark rounded-[28px] p-8 text-white shadow-xl shadow-black/5">
            <div class="flex justify-between items-start mb-8">
                <span class="bg-[#33322f] border border-pf-darkborder text-gray-300 text-xs font-semibold px-4 py-1.5 rounded-full flex items-center gap-2">
                    <i class="fas fa-shield-check text-emerald-400"></i> Secure Public Access
                </span>
                <div class="w-10 h-10 rounded-full bg-[#33322f] border border-pf-darkborder flex items-center justify-center text-gray-400">
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <div class="text-sm font-semibold text-gray-400 mb-2">REF #<?php echo e($ticket->nomor_referensi); ?></div>
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight mb-8">
                <?php echo e($ticket->judul_permintaan ?: 'Permintaan Data Eksternal'); ?>

            </h1>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-pf-darkborder pt-6">
                <div>
                    <div class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Pihak Peminta</div>
                    <div class="font-semibold text-lg"><?php echo e(ucwords(str_replace('_', ' ', $ticket->pihak_peminta))); ?></div>
                </div>
                <div>
                    <div class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Kategori Data</div>
                    <div class="font-semibold text-lg capitalize"><?php echo e(str_replace('_', ' ', $ticket->kategori_data)); ?></div>
                </div>
            </div>
        </section>

        
        <section class="bg-pf-light rounded-[28px] p-8 border border-pf-border">
            
            
            <div class="mb-10">
                <h2 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-align-left text-gray-400"></i> Deskripsi Permintaan
                </h2>
                <div class="text-gray-600 text-sm leading-relaxed whitespace-pre-line bg-gray-50 border border-gray-100 rounded-2xl p-5">
                    <?php echo e($ticket->deskripsi_permintaan); ?>

                </div>
            </div>

            
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-paperclip text-gray-400"></i> Berkas Lampiran
                    </h2>
                    <span class="text-xs font-bold text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                        <?php echo e($ticket->attachments->count()); ?> Files
                    </span>
                </div>
                
                <ul class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $ticket->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-2xl border border-pf-border p-4 hover:border-gray-300 transition-colors">
                            
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 shrink-0">
                                    <i class="fas fa-file-alt text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 break-all"><?php echo e($attachment->file_name); ?></div>
                                    <div class="text-xs text-gray-500 font-medium uppercase mt-1">
                                        <?php echo e(str_replace('_', ' ', $attachment->kategori_lampiran)); ?>

                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                
                                <a href="<?php echo e(route('hr.external-requests.public.preview', ['token' => request()->route('token'), 'attachment' => $attachment])); ?>" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-2 rounded-full border border-pf-border bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                    <i class="fas fa-eye"></i> Preview
                                </a>

                                
                                <?php if(!$shareToken->allow_preview_only && $shareToken->allow_download): ?>
                                    <a href="<?php echo e(route('hr.external-requests.public.download', ['token' => request()->route('token'), 'attachment' => $attachment])); ?>" 
                                       class="inline-flex items-center gap-2 rounded-full bg-pf-dark border border-pf-dark px-4 py-2 text-xs font-semibold text-white hover:bg-pf-darker transition-colors">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="text-center py-10 text-gray-400 text-sm font-medium border border-dashed border-pf-border rounded-2xl">
                            <i class="fas fa-folder-open text-3xl mb-3 text-gray-300 block"></i>
                            Tidak ada berkas yang dilampirkan.
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

        </section>

    </main>

</body>
</html><?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/SystemISPO\resources/views/hr/external-requests/public-show.blade.php ENDPATH**/ ?>