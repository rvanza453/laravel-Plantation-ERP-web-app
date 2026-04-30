@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    .pf-form-wrapper {
        font-family: 'Inter', sans-serif;
        max-width: 1000px;
        margin: 0 auto;
        padding-bottom: 80px;
        color: #111827;
    }

    /* Utilitas Teks */
    .title-md { font-size: 18px; font-weight: 600; letter-spacing: -0.02em; color: #111; margin-bottom: 4px; }
    .text-muted { color: #6b7280; font-size: 13px; font-weight: 500; }

    /* Page Header */
    .pf-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px; flex-wrap: wrap; gap: 20px;}
    .pf-meta { 
        font-size: 12px; font-weight: 600; color: #6b7280; 
        display: flex; align-items: center; gap: 8px; margin-bottom: 8px;
    }
    .pf-title { font-size: 32px; font-weight: 700; letter-spacing: -0.03em; color: #111; line-height: 1.1; }

    /* Card Layout */
    .pf-card-light { 
        background: #ffffff; border-radius: 28px; padding: 32px; 
        border: 1px solid #e5e7eb; margin-bottom: 24px;
    }
    
    .pf-section-title { 
        font-size: 16px; font-weight: 600; color: #111; margin-bottom: 24px; 
        display: flex; align-items: center; gap: 10px; 
    }

    /* Form Elements */
    .pf-grid { display: grid; grid-template-columns: 1fr; gap: 20px; }
    @media (min-width: 768px) { .pf-grid { grid-template-columns: 1fr 1fr; } }
    
    .pf-form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; }
    .pf-label { font-size: 13px; font-weight: 500; color: #4b5563; }
    
    .pf-input, .pf-select, .pf-textarea {
        width: 100%; border-radius: 12px; border: 1px solid #e5e7eb; padding: 12px 16px; 
        font-size: 14px; color: #111; outline: none; transition: all 0.2s; background: #ffffff;
        font-family: inherit;
    }
    .pf-input:focus, .pf-select:focus, .pf-textarea:focus { 
        border-color: #111827; box-shadow: 0 0 0 1px #111827; 
    }
    
    /* Attachment Section */
    .pf-attachment-row { 
        background: #f9fafb; border-radius: 16px; padding: 16px; margin-bottom: 12px; 
        border: 1px solid #e5e7eb; 
    }
    .pf-attachment-grid { display: grid; grid-template-columns: 1fr; gap: 12px; }
    @media (min-width: 640px) { .pf-attachment-grid { grid-template-columns: 1fr 220px 44px; } }

    /* Current Attachments */
    .pf-current-attachment { 
        display: flex; align-items: center; justify-content: space-between; 
        background: #ffffff; padding: 12px 16px; border-radius: 12px; 
        border: 1px solid #e5e7eb; margin-bottom: 12px;
    }

    /* Custom Checkbox untuk hapus file */
    .pf-check-group { display: flex; align-items: center; gap: 8px; cursor: pointer; }
    .pf-check-box { 
        width: 16px; height: 16px; border-radius: 4px; border: 1px solid #ef4444; 
        display: flex; align-items: center; justify-content: center; background: #fff;
    }
    .pf-check-input:checked + .pf-check-box { background: #ef4444; border-color: #ef4444; }
    .pf-check-input:checked + .pf-check-box i { display: block !important; color: #fff; }

    /* Buttons */
    .pf-btn { 
        display: inline-flex; align-items: center; justify-content: center; gap: 8px; 
        padding: 10px 20px; border-radius: 100px; font-size: 14px; font-weight: 600; 
        cursor: pointer; transition: all 0.2s; text-decoration: none; border: 1px solid transparent; 
    }
    .pf-btn-dark { background: #232220; color: #fff; border: 1px solid #232220;}
    .pf-btn-dark:hover { background: #111; }
    
    .pf-btn-light { background: #fff; color: #111; border-color: #e5e7eb; }
    .pf-btn-light:hover { background: #f9fafb; }
    
    .pf-btn-dashed { background: transparent; color: #4b5563; border: 1px dashed #d1d5db; width: 100%; padding: 14px; border-radius: 16px; margin-top: 8px;}
    .pf-btn-dashed:hover { background: #f9fafb; color: #111; border-color: #9ca3af; }

    .pf-btn-danger { background: #fff; color: #6b7280; border-color: #e5e7eb; padding: 0; border-radius: 12px; width: 42px; height: 42px; }
    .pf-btn-danger:hover { background: #fee2e2; color: #ef4444; border-color: #fca5a5; }

    /* Sticky Footer Action */
    .pf-form-actions {
        display: flex; justify-content: flex-end; gap: 12px;
        margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;
    }
</style>

<div class="pf-form-wrapper">
    <div class="pf-header">
        <div>
            <div class="pf-meta">
                <span style="color: #111;"><i class="fas fa-folder-open"></i> Manajemen Data</span>
                <span>•</span>
                <span>{{ $ticket->exists ? 'Edit Tiket' : 'Tiket Baru' }}</span>
            </div>
            <h1 class="pf-title">{{ $ticket->exists ? 'Update Tiket #' . $ticket->nomor_referensi : 'Registrasi Permintaan Data' }}</h1>
        </div>
        <a href="{{ route('hr.external-requests.index') }}" class="pf-btn pf-btn-light">
            Batal
        </a>
    </div>

    @if($errors->any())
        <div style="background: #ffffff; color: #111; padding: 20px 24px; border-radius: 20px; margin-bottom: 24px; border: 1px solid #fca5a5; border-left: 4px solid #ef4444;">
            <div style="font-weight: 600; margin-bottom: 8px; font-size: 14px;"><i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i> Mohon koreksi kesalahan berikut:</div>
            <ul style="font-size: 13px; font-weight: 500; color: #4b5563; padding-left: 24px; list-style-type: disc;">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $ticket->exists ? route('hr.external-requests.update', $ticket) : route('hr.external-requests.store') }}" 
          method="POST" enctype="multipart/form-data">
        @csrf
        @if($ticket->exists) @method('PUT') @endif

        {{-- KARTU 1: Informasi Dasar --}}
        <div class="pf-card-light">
            <div class="pf-section-title">Informasi Dasar</div>
            
            <div class="pf-grid">
                <div class="pf-form-group">
                    <label class="pf-label">Judul Permintaan</label>
                    <input type="text" name="judul_permintaan" value="{{ old('judul_permintaan', $ticket->judul_permintaan ?? '') }}" class="pf-input" placeholder="Contoh: Permintaan Data LH Unit I (opsional)">
                </div>
                <div class="pf-form-group">
                    <label class="pf-label">Pihak Peminta</label>
                    <input type="text" name="pihak_peminta" value="{{ old('pihak_peminta', $ticket->pihak_peminta ?? '') }}" list="pihak-list" class="pf-input" placeholder="Ketik atau pilih pihak peminta...">
                    <datalist id="pihak-list">
                        @foreach($pihakOptions as $option)
                            <option value="{{ ucwords(str_replace('_', ' ', $option)) }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="pf-form-group">
                    <label class="pf-label">Tanggal Surat Masuk</label>
                    <input type="date" name="tanggal_surat_masuk" value="{{ old('tanggal_surat_masuk', $ticket->exists ? optional($ticket->tanggal_surat_masuk)->format('Y-m-d') : '') }}" class="pf-input">
                </div>
                <div class="pf-form-group">
                    <label class="pf-label">Kategori Data</label>
                    <input type="text" name="kategori_data" value="{{ old('kategori_data', $ticket->kategori_data ?? '') }}" list="kategori-list" class="pf-input" placeholder="Ketik atau pilih kategori...">
                    <datalist id="kategori-list">
                        @foreach($kategoriOptions as $option)
                            <option value="{{ strtoupper(str_replace('_', ' ', $option)) }}">
                        @endforeach
                    </datalist>
                </div>
            </div>

            <div class="pf-form-group" style="margin-bottom: 0; margin-top: 4px;">
                <label class="pf-label">Deskripsi Permintaan</label>
                <textarea name="deskripsi_permintaan" rows="4" class="pf-textarea" placeholder="Detailkan data apa saja yang diminta oleh pihak eksternal... (opsional)">{{ old('deskripsi_permintaan', $ticket->deskripsi_permintaan ?? '') }}</textarea>
            </div>
        </div>

        {{-- KARTU 2: Status & Deadline --}}
        <div class="pf-card-light">
            <div class="pf-section-title">Timeline & Status</div>
            <div class="pf-grid" style="margin-bottom: 0;">
                <div class="pf-form-group" style="margin-bottom: 0;">
                    <label class="pf-label">Deadline Penyelesaian</label>
                    <input type="date" name="deadline" value="{{ old('deadline', $ticket->exists ? optional($ticket->deadline)->format('Y-m-d') : '') }}" class="pf-input">
                </div>
                <div class="pf-form-group" style="margin-bottom: 0;">
                    <label class="pf-label">Status Progres</label>
                    <select name="status_proses" class="pf-select">
                        @foreach($statusOptions as $option)
                            <option value="{{ $option }}" @selected(old('status_proses', $ticket->status_proses ?? 'menunggu') === $option)>
                                {{ strtoupper($option) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="pf-form-group" style="margin-bottom: 0;">
                    <label class="pf-label">PIC Internal</label>
                    <select name="pic_user_id" class="pf-select">
                        <option value="">Belum ditentukan</option>
                        @foreach($picUsers as $picUser)
                            <option value="{{ $picUser->id }}" @selected(old('pic_user_id', $ticket->pic_user_id ?? '') == $picUser->id)>
                                {{ $picUser->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- KARTU 3: Lampiran & Catatan --}}
        <div class="pf-card-light" style="margin-bottom: 0;">
            <div class="pf-section-title">Berkas Lampiran</div>
            
            @if($ticket->exists && $ticket->attachments->count() > 0)
                <div style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #f3f4f6;">
                    <label class="pf-label" style="margin-bottom: 12px; display: block; color: #9ca3af; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Berkas Terunggah Saat Ini</label>
                    @foreach($ticket->attachments as $attachment)
                        <div class="pf-current-attachment">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #6b7280;">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div>
                                    <div style="font-size: 13px; font-weight: 500; color: #111;">{{ $attachment->file_name }}</div>
                                </div>
                            </div>
                            
                            <label class="pf-check-group">
                                <span style="font-size: 12px; font-weight: 500; color: #ef4444;">Hapus</span>
                                <div style="position: relative;">
                                    <input type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}" class="pf-check-input" style="opacity: 0; position: absolute; cursor: pointer;">
                                    <div class="pf-check-box"><i class="fas fa-check text-[10px] text-white" style="display: none;"></i></div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif

            <div id="attachments-container">
                <div class="pf-attachment-row">
                    <div class="pf-attachment-grid">
                        <div class="pf-form-group" style="margin-bottom: 0;">
                            <label class="pf-label">Pilih Berkas</label>
                            <input type="file" name="new_attachments[]" class="pf-input" style="padding: 9px 16px; background: #ffffff;">
                        </div>
                        <div class="pf-form-group" style="margin-bottom: 0;">
                            <label class="pf-label">Kategori</label>
                            <select name="new_attachment_categories[]" class="pf-select" style="background: #ffffff;">
                                @foreach($lampiranOptions as $option)
                                    <option value="{{ $option }}">{{ ucwords(str_replace('_', ' ', $option)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="display: flex; align-items: flex-end;">
                            <button type="button" onclick="this.closest('.pf-attachment-row').remove()" class="pf-btn pf-btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" onclick="addNewAttachment()" class="pf-btn pf-btn-dashed">
                <i class="fas fa-plus"></i> Tambah Lampiran Lainnya
            </button>

            {{-- Catatan Internal --}}
            <div class="pf-section-title" style="margin-top: 40px;"><i class="fas fa-lock mr-2" style="color: #9ca3af;"></i> Catatan Internal (Hanya HR)</div>
            <textarea name="catatan_internal" rows="3" class="pf-textarea" placeholder="Tuliskan catatan privasi tim internal jika diperlukan...">{{ old('catatan_internal', $ticket->catatan_internal ?? '') }}</textarea>
            
            <div class="pf-form-actions">
                <button type="submit" class="pf-btn pf-btn-dark" style="padding: 12px 32px;">
                    Simpan Data
                </button>
            </div>
        </div>
    </form>
</div>

<template id="attachment-template">
    <div class="pf-attachment-row">
        <div class="pf-attachment-grid">
            <div class="pf-form-group" style="margin-bottom: 0;">
                <label class="pf-label">Pilih Berkas</label>
                <input type="file" name="new_attachments[]" class="pf-input" style="padding: 9px 16px; background: #ffffff;">
            </div>
            <div class="pf-form-group" style="margin-bottom: 0;">
                <label class="pf-label">Kategori</label>
                <select name="new_attachment_categories[]" class="pf-select" style="background: #ffffff;">
                    @foreach($lampiranOptions as $option)
                        <option value="{{ $option }}">{{ ucwords(str_replace('_', ' ', $option)) }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; align-items: flex-end;">
                <button type="button" onclick="this.closest('.pf-attachment-row').remove()" class="pf-btn pf-btn-danger">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    // Fungsi Clone Template Lampiran Baru
    function addNewAttachment() {
        const container = document.getElementById('attachments-container');
        const template = document.getElementById('attachment-template');
        container.appendChild(template.content.cloneNode(true));
    }

    // Fungsi Custom Checkbox Hapus Berkas
    document.addEventListener('change', function(e) {
        if(e.target && e.target.classList.contains('pf-check-input')) {
            const box = e.target.closest('.pf-check-group').querySelector('.pf-check-box');
            const icon = box.querySelector('i');
            if(e.target.checked) {
                box.style.background = '#ef4444';
                box.style.borderColor = '#ef4444';
                icon.style.display = 'block';
            } else {
                box.style.background = '#ffffff';
                box.style.borderColor = '#e5e7eb';
                icon.style.display = 'none';
            }
        }
    });

    // Init checkbox
    document.querySelectorAll('.pf-check-input').forEach(input => {
        if(input.checked) input.dispatchEvent(new Event('change', {bubbles:true}));
    });
</script>
@endsection