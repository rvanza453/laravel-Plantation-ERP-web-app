<?php if (isset($component)) { $__componentOriginal8ffca6aaed06613cf5643e13e74c8806 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806 = $attributes; } ?>
<?php $component = Modules\ServiceAgreementSystem\View\Components\Layouts\Master::resolve(['title' => 'Budget USPK'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('serviceagreementsystem::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Modules\ServiceAgreementSystem\View\Components\Layouts\Master::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('actions'); ?>
        <a href="<?php echo e(route('sas.uspk.index')); ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-file-signature"></i> Ke Pengajuan USPK
        </a>
    <?php $__env->stopPush(); ?>

    <div class="card mb-4">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-filter" style="color: var(--accent); margin-right: 8px;"></i>Filter Arsip Budget</div>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('sas.uspk-budgets.index')); ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tahun</label>
                        <input type="number" min="2000" max="2100" class="form-control" name="year" value="<?php echo e($selectedYear); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Site / Department</label>
                        <select name="department_id" class="form-control">
                            <option value="">-- Semua Department --</option>
                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($department->id); ?>" <?php echo e((string) $selectedDepartment === (string) $department->id ? 'selected' : ''); ?>>
                                    <?php echo e($department->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Tampilkan Arsip
                    </button>
                    <a href="<?php echo e(route('sas.uspk-budgets.index', ['year' => now()->year])); ?>" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-wallet" style="color: var(--success); margin-right: 8px;"></i>Input Budget USPK Tahunan</div>
            <span class="badge badge-approved">Tahun <?php echo e($selectedYear); ?></span>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo e(route('sas.uspk-budgets.store')); ?>" id="budgetForm">
                <?php echo csrf_field(); ?>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Tahun Budget</label>
                        <input type="number" min="2000" max="2100" class="form-control" name="year" id="year" value="<?php echo e(old('year', $selectedYear)); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Site / Department</label>
                        <select name="department_id" id="department_id" class="form-control" required>
                            <option value="">-- Pilih Site --</option>
                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($department->id); ?>" <?php echo e((string) old('department_id', $selectedDepartment) === (string) $department->id ? 'selected' : ''); ?>>
                                    <?php echo e($department->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Afdeling</label>
                        <select name="sub_department_id" id="sub_department_id" class="form-control" required>
                            <option value="">-- Pilih Afdeling --</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="d-flex justify-between align-center mb-2">
                        <label class="form-label required mb-0">Daftar Job dan Budget</label>
                        <button type="button" class="btn btn-secondary btn-sm" id="addRowBtn">
                            <i class="fas fa-plus"></i> Tambah Job
                        </button>
                    </div>

                    <div id="rowsContainer"></div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Budget Tahunan
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-archive" style="color: var(--warning); margin-right: 8px;"></i>
                Arsip Budget USPK Tahun <?php echo e($selectedYear); ?>

            </div>
            <span class="text-muted" style="font-size: 12px;"><?php echo e($budgets->count()); ?> data</span>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Site / Department</th>
                        <th>Afdeling</th>
                        <th>Job</th>
                        <th class="text-right">Budget</th>
                        <th class="text-right">Terpakai</th>
                        <th class="text-right">Sisa</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $budgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $budget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($budget->subDepartment?->department?->name ?? '-'); ?></td>
                            <td><?php echo e($budget->subDepartment?->name ?? '-'); ?></td>
                            <td><?php echo e($budget->job?->name ?? '-'); ?></td>
                            <td class="text-right"><?php echo e(number_format((float) $budget->budget_amount, 0, ',', '.')); ?></td>
                            <td class="text-right"><?php echo e(number_format((float) $budget->used_amount, 0, ',', '.')); ?></td>
                            <td class="text-right" style="font-weight: 600; color: var(--success);"><?php echo e(number_format((float) ($budget->budget_amount - $budget->used_amount), 0, ',', '.')); ?></td>
                            <td><?php echo e($budget->description ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>Belum ada budget untuk filter tahun ini.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        <?php
            $initialBudgetRows = old('rows', [['job_id' => '', 'budget_amount' => '', 'description' => '']]);
        ?>

        const departmentsData = <?php echo json_encode($departments, 15, 512) ?>;
        const subDepartments = <?php echo json_encode($subDepartments, 15, 512) ?>;
        const jobsData = <?php echo json_encode($jobs, 15, 512) ?>;
        const oldRows = <?php echo json_encode($initialBudgetRows, 15, 512) ?>;
        const oldSubDepartmentId = <?php echo json_encode(old('sub_department_id'), 15, 512) ?>;

        const departmentSelect = document.getElementById('department_id');
        const subDepartmentSelect = document.getElementById('sub_department_id');
        const rowsContainer = document.getElementById('rowsContainer');

        function getSiteIdByDepartment(departmentId) {
            const department = departmentsData.find(item => String(item.id) === String(departmentId));
            return department ? String(department.site_id ?? '') : '';
        }

        function renderSubDepartments() {
            const departmentId = departmentSelect.value;
            subDepartmentSelect.innerHTML = '<option value="">-- Pilih Afdeling --</option>';

            if (!departmentId) {
                refreshJobOptions();
                return;
            }

            subDepartments
                .filter(item => String(item.department_id) === String(departmentId))
                .forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    if (oldSubDepartmentId && String(oldSubDepartmentId) === String(item.id)) {
                        option.selected = true;
                    }
                    subDepartmentSelect.appendChild(option);
                });

            refreshJobOptions();
        }

        function jobOptions(selectedId) {
            const siteId = getSiteIdByDepartment(departmentSelect.value);
            let options = '<option value="">-- Pilih Job --</option>';

            if (!siteId) {
                return options;
            }

            jobsData
                .filter(job => String(job.site_id ?? '') === String(siteId))
                .forEach(job => {
                    const selected = String(selectedId) === String(job.id) ? 'selected' : '';
                    const label = `${job.code ? `${job.code} - ` : ''}${job.name}`;
                    options += `<option value="${job.id}" ${selected}>${label}</option>`;
                });

            return options;
        }

        function refreshJobOptions() {
            rowsContainer.querySelectorAll('select[name$="[job_id]"]').forEach(select => {
                const selectedId = select.value;
                select.innerHTML = jobOptions(selectedId);
            });
        }

        function addBudgetRow(rowData = null) {
            const index = rowsContainer.querySelectorAll('.budget-row').length;
            const selectedJob = rowData?.job_id ?? '';
            const amount = rowData?.budget_amount ?? '';
            const description = rowData?.description ?? '';

            const row = document.createElement('div');
            row.className = 'budget-row';
            row.style.cssText = 'border: 1px solid var(--border-color); border-radius: 10px; padding: 16px; margin-bottom: 12px; background: var(--bg-input);';
            row.innerHTML = `
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Job</label>
                        <select class="form-control" name="rows[${index}][job_id]" required>
                            ${jobOptions(selectedJob)}
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Budget (Rp)</label>
                        <input type="number" class="form-control" name="rows[${index}][budget_amount]" min="0" step="0.01" value="${amount}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" name="rows[${index}][description]" value="${description}">
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-row-btn" style="margin-top: 4px;">
                    <i class="fas fa-trash"></i> Hapus Baris
                </button>
            `;

            row.querySelector('.remove-row-btn').addEventListener('click', () => {
                row.remove();
                reindexRows();
            });

            rowsContainer.appendChild(row);
        }

        function reindexRows() {
            const rows = rowsContainer.querySelectorAll('.budget-row');
            rows.forEach((row, index) => {
                row.querySelectorAll('[name]').forEach(input => {
                    input.name = input.name.replace(/rows\[\d+\]/, `rows[${index}]`);
                });
            });
        }

        document.getElementById('addRowBtn').addEventListener('click', () => {
            addBudgetRow();
        });

        departmentSelect.addEventListener('change', () => {
            renderSubDepartments();
            refreshJobOptions();
        });

        renderSubDepartments();

        if (Array.isArray(oldRows) && oldRows.length > 0) {
            oldRows.forEach(row => addBudgetRow(row));
        } else {
            addBudgetRow();

        refreshJobOptions();
        }
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
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/ServiceAgreementSystem\resources/views/uspk-budget/index.blade.php ENDPATH**/ ?>