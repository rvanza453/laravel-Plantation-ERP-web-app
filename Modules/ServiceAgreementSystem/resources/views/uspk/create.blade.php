<x-serviceagreementsystem::layouts.master :title="'Buat Pengajuan USPK'">
    
    @push('actions')
        <a href="{{ route('sas.uspk.index') }}" class="btn btn-secondary btn-sm" style="border-radius: 8px; font-weight: 500;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    @endpush

    {{-- Kumpulan Custom Styling --}}
    <style>
        /* Card Enhancement */
        .card.modern-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            background-color: #ffffff;
            margin-bottom: 1.5rem;
        }
        .modern-card .card-header {
            background-color: transparent;
            border-bottom: 1px solid #f3f4f6;
            padding: 1.25rem 1.5rem;
        }
        .modern-card .card-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        /* Input & Label Enhancement */
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            font-size: 0.9rem;
        }
        .form-label.required::after {
            content: " *";
            color: #ef4444;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 10px 14px;
            min-height: 42px;
            background-color: #f9fafb;
            color: #1f2937;
        }
        .form-control:focus {
            background-color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        /* Styling panah untuk select biasa */
        select.form-control:not([multiple]) {
            appearance: none;
            -moz-appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 16px;
            padding-right: 2.5rem;
        }

        /* Select2 override agar senada */
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            background-color: #f9fafb !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            min-height: 42px !important;
            padding: 6px 10px;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--focus .select2-selection--single {
            background-color: #ffffff !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 8px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            color: #1f2937 !important;
            padding-left: 0 !important;
        }
        
        /* Tender Box Styling */
        .tender-box {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #3b82f6;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        
        /* Alert Info */
        .alert-info-custom {
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        /* Radio Button Winner Override */
        .winner-radio-wrapper {
            background: #f0fdf4; 
            padding: 6px 12px; 
            border-radius: 8px; 
            border: 1px solid #bbf7d0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            margin: 0;
            transition: all 0.2s ease;
        }
        .winner-radio-wrapper input {
            cursor: pointer;
            width: 16px;
            height: 16px;
            margin: 0;
        }
        
        /* Utility Classes */
        .gap-2 { gap: 0.5rem; }
    </style>

    <form action="{{ route('sas.uspk.store') }}" method="POST" enctype="multipart/form-data" id="uspkForm">
        @csrf

        {{-- Informasi Pekerjaan --}}
        <div class="card modern-card">
            <div class="card-header">
                <div class="card-title">
                    <div style="background: #e0e7ff; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin-right: 10px;">
                        <i class="fas fa-info-circle" style="color: #4f46e5; font-size: 1rem;"></i>
                    </div>
                    Informasi Pekerjaan
                </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label required">Nama Kegiatan</label>
                    <select id="namaKegiatanSelect" class="form-control">
                        <option value="">-- Pilih Kegiatan --</option>
                        <option value="Pemeliharaan Jalan">Pemeliharaan Jalan</option>
                        <option value="Pembersihan Parit / Saluran Air">Pembersihan Parit / Saluran Air</option>
                        <option value="Pemupukan Tanaman">Pemupukan Tanaman</option>
                        <option value="Penyemprotan Herbisida">Penyemprotan Herbisida</option>
                        <option value="Panen Buah Sawit">Panen Buah Sawit</option>
                        <option value="Pengendalian Hama Terpadu">Pengendalian Hama Terpadu</option>
                        <option value="Pembangunan / Perbaikan Infrastruktur">Pembangunan / Perbaikan Infrastruktur</option>
                        <option value="Perawatan Alat Berat">Perawatan Alat Berat</option>
                        <option value="Lainnya">Isi Sendiri / Lainnya...</option>
                    </select>
                    <input type="hidden" name="title" id="titleInput" value="{{ old('title') }}">
                    <div id="customTitleWrapper" style="margin-top: 10px; display: none;">
                        <input type="text" id="customTitleInput" class="form-control" placeholder="Tuliskan nama kegiatan..." value="{{ old('title') }}">
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label class="form-label">Deskripsi / Uraian Pekerjaan</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan secara detail pekerjaan yang akan diborongkan...">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Lokasi & Aktivitas --}}
        <div class="card modern-card">
            <div class="card-header">
                <div class="card-title">
                    <div style="background: #dcfce7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin-right: 10px;">
                        <i class="fas fa-map-marker-alt" style="color: #16a34a; font-size: 1rem;"></i>
                    </div>
                    Lokasi & Aktivitas
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Site / Department</label>
                            <select name="department_id" id="department_id" class="form-control" required>
                                <option value="">-- Pilih Site --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Afdeling</label>
                            <select name="sub_department_id" id="sub_department_id" class="form-control" required>
                                <option value="">-- Pilih Afdeling --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Blok <span style="font-size: 11px; color: #6b7280; font-weight: normal;">(Bisa pilih >1)</span></label>
                            <select name="block_ids[]" id="block_ids" class="form-control select2-searchable" multiple required>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Aktivitas (Job)</label>
                            <select name="job_id" id="job_id" class="form-control select2-searchable" required>
                                <option value="">-- Pilih Aktivitas --</option>
                                @foreach($jobs as $job)
                                    <option value="{{ $job->id }}" {{ old('job_id') == $job->id ? 'selected' : '' }}>
                                        {{ $job->code ? $job->code . ' - ' : '' }}{{ $job->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label class="form-label">Budget Aktivitas</label>
                    <select name="uspk_budget_activity_id" id="uspk_budget_activity_id" class="form-control select2-searchable">
                        <option value="">-- Pilih Budget (opsional) --</option>
                    </select>
                    <div id="budgetInfo" style="margin-top: 8px; font-size: 13px; background: #f0fdf4; padding: 8px 12px; border-radius: 6px; border: 1px solid #bbf7d0; color: #15803d; display: none;">
                        <i class="fas fa-wallet mr-1"></i> Sisa Budget dialokasikan: <span id="budgetRemaining" style="font-weight: 700;">-</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tender Pembanding --}}
        <div class="card modern-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title">
                    <div style="background: #fef3c7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin-right: 10px;">
                        <i class="fas fa-balance-scale" style="color: #d97706; font-size: 1rem;"></i>
                    </div>
                    Tender Pembanding
                </div>
                <button type="button" id="addTender" class="btn btn-primary btn-sm" onclick="addTenderRow()" style="border-radius: 6px; font-weight: 500;">
                    <i class="fas fa-plus"></i> Tambah Tender
                </button>
            </div>
            <div class="card-body">
                <div class="alert-info-custom">
                    <i class="fas fa-info-circle" style="font-size: 1.1rem; margin-top: 2px;"></i>
                    <div>
                        Minimal 1, maksimal 3 tender pembanding. Nilai dan durasi diisi sesuai tawaran masing-masing kontraktor. Pilih salah satu sebagai tender yang diajukan dengan memilih opsi <b>Rekomendasi Kontraktor</b>.
                    </div>
                </div>

                <div id="tendersContainer">
                    {{-- Tender row 1 (default) --}}
                    <div class="tender-box" data-index="0">
                        {{-- Ditambahkan margin-left: auto; untuk mendorong radio button ke ujung kanan --}}
                        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center mb-3 pb-3" style="border-bottom: 1px dashed #e5e7eb; gap: 10px;">
                            <div style="font-weight: 700; font-size: 15px; color: #1f2937;">
                                <span style="background: #e5e7eb; color: #4b5563; padding: 3px 8px; border-radius: 6px; font-size: 12px; margin-right: 5px;">#1</span> 
                                <span class="tender-title-text">Penawaran Pertama</span>
                            </div>
                            <div class="d-flex align-items-center gap-2" style="margin-left: auto;">
                                <label class="winner-radio-wrapper">
                                    <input type="radio" name="selected_tender" value="0" checked>
                                    <span class="winner-label" style="color: #16a34a; font-weight: 600; font-size: 13px;">Rekomendasi Kontraktor</span>
                                </label>
                            </div>
                        </div>

                        <input type="hidden" name="tenders[0][is_selected]" value="0" id="tender_selected_0">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">Kontraktor</label>
                                    <select name="tenders[0][contractor_id]" class="form-control select2-searchable contractor-select" required>
                                        <option value="">-- Cari & Pilih Kontraktor --</option>
                                        @foreach($contractors as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }} {{ $c->company_name ? '(' . $c->company_name . ')' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">Nilai Tender (Rp)</label>
                                    <input type="number" name="tenders[0][tender_value]" class="form-control" placeholder="Contoh: 15000000" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Durasi (Hari)</label>
                                    <input type="number" name="tenders[0][tender_duration]" class="form-control" placeholder="Contoh: 30" min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Lampiran Penawaran</label>
                                    <input type="file" name="tenders[0][attachment]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" style="padding: 7px 12px;">
                                    <small class="text-muted" style="font-size: 11px; margin-top: 4px; display: block;">Format PDF/JPG/PNG max 5MB</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Keterangan</label>
                            <textarea name="tenders[0][description]" class="form-control" rows="2" placeholder="Tulis catatan tambahan terkait spesifikasi tender ini (opsional)..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Buttons Dibuat Rata Kanan --}}
        <div class="d-flex justify-content-end align-items-center mb-5" style="gap: 15px;">
            <a href="{{ route('sas.uspk.index') }}" class="btn text-danger" style="font-weight: 600;">Batal</a>
            <button type="submit" name="save_draft" value="1" class="btn btn-outline-secondary" style="padding: 10px 24px; border-radius: 8px; font-weight: 600; background-color: white;">
                <i class="fas fa-save mr-1"></i> Simpan Draft
            </button>
            <button type="submit" name="submit_now" value="1" class="btn btn-primary" style="padding: 10px 24px; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-paper-plane mr-1"></i> Ajukan Pengajuan
            </button>
        </div>
    </form>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        let tenderCount = 1;
        const maxTenders = 3;
        const contractorsData = @json($contractors);
        const departmentsData = @json($departments);
        const jobsData = @json($jobs);
        const apiSubDepartmentsUrlTemplate = @json(route('sas.api.sub-departments', ['departmentId' => '__DEPARTMENT_ID__']));
        const apiBlocksUrlTemplate = @json(route('sas.api.blocks', ['subDepartmentId' => '__SUB_DEPARTMENT_ID__']));
        const apiBudgetActivitiesBaseUrl = @json(route('sas.api.budget-activities'));
        
        const selectedBudgetId = @json(old('uspk_budget_activity_id'));
        const initialDepartmentId = @json(old('department_id'));
        const initialJobId = @json(old('job_id'));
        const initialSubDepartmentId = @json(old('sub_department_id'));
        const initialBlockIds = (@json(old('block_ids', [])) || []).map(String);

        const budgetInfo = document.getElementById('budgetInfo');
        const budgetRemaining = document.getElementById('budgetRemaining');

        $(document).ready(function() {
            $('.select2-searchable').select2({ width: '100%' });

            handleNamaKegiatanChange(document.getElementById('namaKegiatanSelect'));
            updateAddButton();

            if (initialDepartmentId) {
                renderJobOptions(initialJobId);
                fetchSubDepartments(initialDepartmentId, initialSubDepartmentId).then(() => {
                    if (initialSubDepartmentId) {
                        fetchBlocks(initialSubDepartmentId, initialBlockIds);
                        fetchBudgets(initialSubDepartmentId, initialJobId, selectedBudgetId);
                    }
                });
            }
        });

        function handleNamaKegiatanChange(select) {
            const val = select.value;
            const customWrapper = document.getElementById('customTitleWrapper');
            const titleInput = document.getElementById('titleInput');
            const customInput = document.getElementById('customTitleInput');

            if (val === 'Lainnya' || val === '') {
                customWrapper.style.display = val === 'Lainnya' ? 'block' : 'none';
                titleInput.value = customInput.value;
                customInput.oninput = () => titleInput.value = customInput.value;
            } else {
                customWrapper.style.display = 'none';
                titleInput.value = val;
            }
        }

        const oldTitle = @json(old('title', ''));
        if (oldTitle) {
            const select = document.getElementById('namaKegiatanSelect');
            let matched = false;
            for (let opt of select.options) {
                if (opt.value === oldTitle) { opt.selected = true; matched = true; break; }
            }
            if (!matched && oldTitle) {
                select.value = 'Lainnya';
                document.getElementById('customTitleWrapper').style.display = 'block';
                document.getElementById('customTitleInput').value = oldTitle;
            }
            $(select).trigger('change'); 
        }

        function getSiteIdByDepartment(departmentId) {
            const department = departmentsData.find(item => String(item.id) === String(departmentId));
            return department ? String(department.site_id ?? '') : '';
        }

        function renderJobOptions(selectedJobId = '') {
            const departmentVal = $('#department_id').val();
            const siteId = getSiteIdByDepartment(departmentVal);
            let options = '<option value="">-- Pilih Aktivitas --</option>';

            if (!siteId) {
                $('#job_id').html(options).trigger('change.select2');
                return;
            }

            jobsData
                .filter(job => String(job.site_id ?? '') === String(siteId))
                .forEach(job => {
                    const selected = String(selectedJobId) === String(job.id) ? 'selected' : '';
                    const label = `${job.code ? `${job.code} - ` : ''}${job.name}`;
                    options += `<option value="${job.id}" ${selected}>${label}</option>`;
                });

            $('#job_id').html(options).trigger('change.select2');
        }

        function addTenderRow() {
            if (tenderCount >= maxTenders) {
                alert('Maksimal 3 tender pembanding.');
                return;
            }

            const idx = tenderCount;
            const contractorsOptions = contractorsData
                .map(c => `<option value="${c.id}">${c.name}${c.company_name ? ` (${c.company_name})` : ''}</option>`)
                .join('');

            const row = document.createElement('div');
            row.className = 'tender-box';
            row.dataset.index = String(idx);
            
            // Baris dinamis juga ditambahkan margin-left: auto;
            row.innerHTML = `
                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center mb-3 pb-3" style="border-bottom: 1px dashed #e5e7eb; gap: 10px;">
                    <div style="font-weight: 700; font-size: 15px; color: #1f2937;">
                        <span style="background: #e5e7eb; color: #4b5563; padding: 3px 8px; border-radius: 6px; font-size: 12px; margin-right: 5px;">#${idx + 1}</span> 
                        <span class="tender-title-text">Penawaran Alternatif</span>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="margin-left: auto;">
                        <label class="winner-radio-wrapper" style="background: transparent; border-color: transparent;">
                            <input type="radio" name="selected_tender" value="${idx}" onchange="highlightWinner()">
                            <span class="winner-label" style="color: #9ca3af; font-weight: 600; font-size: 13px;">Rekomendasi Kontraktor</span>
                        </label>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeTenderRow(${idx})" style="border-radius: 6px; padding: 6px 10px;" title="Hapus Tender">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <input type="hidden" name="tenders[${idx}][is_selected]" value="0" id="tender_selected_${idx}">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Kontraktor</label>
                            <select name="tenders[${idx}][contractor_id]" class="form-control select2-searchable contractor-select" required>
                                <option value="">-- Cari & Pilih Kontraktor --</option>
                                ${contractorsOptions}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label required">Nilai Tender (Rp)</label>
                            <input type="number" name="tenders[${idx}][tender_value]" class="form-control" placeholder="Contoh: 15000000" step="0.01" min="0" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Durasi (Hari)</label>
                            <input type="number" name="tenders[${idx}][tender_duration]" class="form-control" placeholder="Contoh: 30" min="1">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Lampiran Penawaran</label>
                            <input type="file" name="tenders[${idx}][attachment]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" style="padding: 7px 12px;">
                            <small class="text-muted" style="font-size: 11px; margin-top: 4px; display: block;">Format PDF/JPG/PNG max 5MB</small>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label class="form-label">Keterangan</label>
                    <textarea name="tenders[${idx}][description]" class="form-control" rows="2" placeholder="Tulis catatan tambahan terkait spesifikasi tender ini (opsional)..."></textarea>
                </div>
            `;

            document.getElementById('tendersContainer').appendChild(row);
            tenderCount++;
            updateAddButton();

            $(row).find('.contractor-select').select2({ width: '100%' });
            highlightWinner();
        }

        function removeTenderRow(index) {
            const row = document.querySelector(`.tender-box[data-index="${index}"]`);
            if (!row) return;

            row.remove();
            
            const tenderBoxes = document.querySelectorAll('.tender-box');
            tenderCount = tenderBoxes.length;
            
            tenderBoxes.forEach((el, newIdx) => {
                const numberSpan = el.querySelector('.tender-title-text').previousElementSibling;
                if(numberSpan) numberSpan.textContent = `#${newIdx + 1}`;
            });

            updateAddButton();
        }

        function updateAddButton() {
            const addBtn = document.getElementById('addTender');
            addBtn.style.display = tenderCount >= maxTenders ? 'none' : '';
        }
        
        function highlightWinner() {
            document.querySelectorAll('input[name="selected_tender"]').forEach(radio => {
                const labelContainer = radio.closest('.winner-radio-wrapper');
                const labelText = labelContainer.querySelector('.winner-label');
                if(radio.checked) {
                    labelContainer.style.borderColor = '#bbf7d0';
                    labelContainer.style.backgroundColor = '#f0fdf4';
                    labelText.style.color = '#16a34a';
                } else {
                    labelContainer.style.borderColor = 'transparent';
                    labelContainer.style.backgroundColor = 'transparent';
                    labelText.style.color = '#9ca3af';
                }
            });
        }
        
        $(document).on('change', 'input[name="selected_tender"]', highlightWinner);

        async function fetchSubDepartments(departmentId, selectedSubDeptId = '') {
            if (!departmentId) {
                $('#sub_department_id').html('<option value="">-- Pilih Afdeling --</option>');
                return;
            }
            const url = apiSubDepartmentsUrlTemplate.replace('__DEPARTMENT_ID__', encodeURIComponent(String(departmentId)));
            $('#sub_department_id').html('<option value="">Memuat...</option>');
            
            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error('Gagal mengambil data afdeling');
                const subDepartments = await response.json();

                let options = '<option value="">-- Pilih Afdeling --</option>';
                subDepartments.forEach(sub => {
                    const selected = String(selectedSubDeptId) === String(sub.id) ? 'selected' : '';
                    options += `<option value="${sub.id}" ${selected}>${sub.name}</option>`;
                });
                
                $('#sub_department_id').html(options);
            } catch (error) {
                console.error('Error fetching sub-departments:', error);
                $('#sub_department_id').html('<option value="">Gagal memuat</option>');
            }
        }

        async function fetchBlocks(subDepartmentId, selectedBlockIds = []) {
            if (!subDepartmentId) {
                $('#block_ids').empty().trigger('change.select2');
                return;
            }
            const url = apiBlocksUrlTemplate.replace('__SUB_DEPARTMENT_ID__', encodeURIComponent(String(subDepartmentId)));
            $('#block_ids').html('<option value="">Memuat...</option>').trigger('change.select2');
            
            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error('Gagal mengambil data blok');
                const blocks = await response.json();

                let options = '';
                blocks.forEach(block => {
                    const selected = selectedBlockIds.includes(String(block.id)) ? 'selected' : '';
                    options += `<option value="${block.id}" ${selected}>${block.name} (${block.code})</option>`;
                });
                
                $('#block_ids').html(options).trigger('change.select2');
            } catch (error) {
                console.error('Error fetching blocks:', error);
                $('#block_ids').html('<option value="">Gagal memuat</option>').trigger('change.select2');
            }
        }

        async function fetchBudgets(subDepartmentId, jobId, selectedBudgetId = '') {
            if (!subDepartmentId) {
                $('#uspk_budget_activity_id').html('<option value="">-- Pilih Budget (opsional) --</option>').trigger('change.select2');
                budgetInfo.style.display = 'none';
                return;
            }

            const params = new URLSearchParams({
                sub_department_id: String(subDepartmentId),
                year: String(new Date().getFullYear())
            });

            if (jobId) {
                params.append('job_id', String(jobId));
            }

            const url = `${apiBudgetActivitiesBaseUrl}?${params.toString()}`;
            $('#uspk_budget_activity_id').html('<option value="">Memuat...</option>').trigger('change.select2');
            
            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error('Gagal mengambil data budget');
                const budgets = await response.json();

                let options = '<option value="">-- Pilih Budget (opsional) --</option>';
                budgets.forEach(budget => {
                    const remaining = Number(budget.budget_amount) - Number(budget.used_amount);
                    const formattedRemaining = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(remaining);
                    const selected = String(selectedBudgetId) === String(budget.id) ? 'selected' : '';
                    options += `<option value="${budget.id}" data-remaining="${remaining}" ${selected}>${budget.job?.name ?? 'Job'} - Total: ${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(Number(budget.budget_amount))} | Sisa: ${formattedRemaining}</option>`;
                });
                
                $('#uspk_budget_activity_id').html(options).trigger('change.select2');
                updateBudgetInfo();
            } catch (error) {
                console.error('Error fetching budgets:', error);
                $('#uspk_budget_activity_id').html('<option value="">Gagal memuat</option>').trigger('change.select2');
                budgetInfo.style.display = 'none';
            }
        }

        function updateBudgetInfo() {
            const budgetSelect = document.getElementById('uspk_budget_activity_id');
            const selectedOption = budgetSelect.options[budgetSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const remaining = selectedOption.getAttribute('data-remaining');
                const formattedRemaining = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(remaining);
                budgetRemaining.textContent = formattedRemaining;
                
                if(remaining < 0) {
                    budgetRemaining.style.color = '#dc2626';
                    budgetInfo.style.backgroundColor = '#fef2f2';
                    budgetInfo.style.borderColor = '#fecaca';
                    budgetInfo.style.color = '#991b1b';
                } else {
                    budgetRemaining.style.color = 'inherit';
                    budgetInfo.style.backgroundColor = '#f0fdf4';
                    budgetInfo.style.borderColor = '#bbf7d0';
                    budgetInfo.style.color = '#15803d';
                }
                
                budgetInfo.style.display = 'block';
            } else {
                budgetInfo.style.display = 'none';
            }
        }

        $('#namaKegiatanSelect').on('change', function() {
            handleNamaKegiatanChange(this);
        });

        $('#department_id').on('change', function() {
            renderJobOptions();
            fetchSubDepartments(this.value);
            $('#block_ids').empty().trigger('change.select2');
            $('#uspk_budget_activity_id').html('<option value="">-- Pilih Budget (opsional) --</option>').trigger('change.select2');
            budgetInfo.style.display = 'none';
        });

        $('#sub_department_id').on('change', function() {
            fetchBlocks(this.value);
            fetchBudgets(this.value, $('#job_id').val());
        });

        $('#job_id').on('change', function() {
            fetchBudgets($('#sub_department_id').val(), this.value);
        });

        $('#uspk_budget_activity_id').on('change', updateBudgetInfo);

        document.getElementById('uspkForm').addEventListener('submit', function() {
            document.querySelectorAll('[id^="tender_selected_"]').forEach(el => {
                el.value = '0';
            });

            const selected = document.querySelector('input[name="selected_tender"]:checked');
            if (selected) {
                const target = document.getElementById(`tender_selected_${selected.value}`);
                if (target) {
                    target.value = '1';
                }
            }
        });
    </script>
    @endpush
</x-serviceagreementsystem::layouts.master>