<?php

use Illuminate\Support\Facades\Route;
use Modules\ServiceAgreementSystem\Http\Controllers\DashboardController;
use Modules\ServiceAgreementSystem\Http\Controllers\ContractorController;
use Modules\ServiceAgreementSystem\Http\Controllers\UspkSubmissionController;
use Modules\ServiceAgreementSystem\Http\Controllers\UspkApprovalController;
use Modules\ServiceAgreementSystem\Http\Controllers\UspkApprovalSchemaController;
use Modules\ServiceAgreementSystem\Http\Controllers\UspkBudgetController;
use Modules\ServiceAgreementSystem\Http\Controllers\UspkLegalController;
use Modules\ServiceAgreementSystem\Http\Controllers\UspkQcController;
use Modules\ServiceAgreementSystem\Http\Controllers\UspkBappController;

// Authenticated routes
Route::middleware(['auth', 'assigned.role', 'sas.role'])->prefix('sas')->name('sas.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Kontraktor CRUD (Admin only)
    Route::resource('contractors', ContractorController::class)->middleware('sas.role:Admin');

    Route::resource('uspk', UspkSubmissionController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('sas.role:Staff,Admin');

    // USPK Submissions
    Route::resource('uspk', UspkSubmissionController::class)
        ->only(['index', 'show'])
        ->middleware('sas.role:Staff,Approver,Legal,QC,Admin');

    Route::post('uspk/{uspk}/submit', [UspkSubmissionController::class, 'submit'])
        ->name('uspk.submit')
        ->middleware('sas.role:Staff,Admin');

    // USPK Budgeting (Admin only)
    Route::get('uspk-budgets', [UspkBudgetController::class, 'index'])
        ->name('uspk-budgets.index')
        ->middleware('sas.role:Admin');
    Route::post('uspk-budgets', [UspkBudgetController::class, 'store'])
        ->name('uspk-budgets.store')
        ->middleware('sas.role:Admin');
        
    // Unified Task & Approval Hub
    Route::get('tasks', [\Modules\ServiceAgreementSystem\Http\Controllers\UspkTaskController::class, 'index'])
        ->name('tasks.index')
        ->middleware('sas.role:Staff,Approver,Legal,QC,Admin');

    // USPK Approvals (Approver/Admin)
    Route::get('uspk-approvals', [UspkApprovalController::class, 'index'])
        ->name('uspk-approvals.index')
        ->middleware('sas.role:Approver,Admin');
    Route::post('uspk/{uspk}/approve', [UspkApprovalController::class, 'approve'])
        ->name('uspk.approve')
        ->middleware('sas.role:Approver,Admin');
    Route::post('uspk/{uspk}/hold', [UspkApprovalController::class, 'hold'])
        ->name('uspk.hold')
        ->middleware('sas.role:Approver,Admin');
    Route::post('uspk/{uspk}/reject', [UspkApprovalController::class, 'reject'])
        ->name('uspk.reject')
        ->middleware('sas.role:Approver,Admin');
    Route::post('uspk/{uspk}/rollback-approval', [UspkApprovalController::class, 'rollback'])
        ->name('uspk.rollback-approval')
        ->middleware('sas.role:Admin');

    // USPK Legal SPK Workflow (Legal/Admin)
    Route::get('uspk-legal', [UspkLegalController::class, 'index'])
        ->name('uspk-legal.index')
        ->middleware('sas.role:Legal,Admin');
    Route::get('uspk/{uspk}/legal/export-spk', [UspkLegalController::class, 'exportDraft'])
        ->name('uspk-legal.export')
        ->middleware('sas.role:Legal,Admin');
    Route::post('uspk/{uspk}/legal/upload-spk', [UspkLegalController::class, 'uploadFinal'])
        ->name('uspk-legal.upload')
        ->middleware('sas.role:Legal,Admin');
    Route::get('uspk/{uspk}/legal/download-spk', [UspkLegalController::class, 'downloadFinal'])
        ->name('uspk-legal.download')
        ->middleware('sas.role:Legal,Admin');
    Route::post('uspk/{uspk}/legal/return-to-selection', [UspkLegalController::class, 'returnToSelection'])
        ->name('uspk-legal.return')
        ->middleware('sas.role:Legal,Admin');

    // USPK QC Workflow
    Route::get('uspk-qc', [UspkQcController::class, 'index'])
        ->name('uspk-qc.index')
        ->middleware('sas.role:QC,Admin,Staff');
    Route::post('uspk/{uspk}/submitter/upload-signed-spk', [UspkQcController::class, 'uploadSignedBySubmitter'])
        ->name('uspk-qc.upload-signed')
        ->middleware('sas.role:Staff,Admin');
    Route::post('uspk/{uspk}/qc/assign-verifiers', [UspkQcController::class, 'assignVerifiers'])
        ->name('uspk-qc.assign-verifiers')
        ->middleware('sas.role:QC,Admin');
    Route::post('uspk/{uspk}/qc/report-completed', [UspkQcController::class, 'reportWorkCompleted'])
        ->name('uspk-qc.report-completed')
        ->middleware('sas.role:Staff,Admin');
    Route::post('uspk/{uspk}/qc/block-progress', [UspkQcController::class, 'saveBlockProgress'])
        ->name('uspk-qc.block-progress')
        ->middleware('sas.role:QC,Admin,Staff');
    Route::post('uspk/{uspk}/qc/verify', [UspkQcController::class, 'verifyWork'])
        ->name('uspk-qc.verify')
        ->middleware('sas.role:QC,Admin,Staff');

    // USPK Approval Schemas Configuration (Admin only)
    Route::resource('approval-schemas', UspkApprovalSchemaController::class)
        ->except(['show'])
        ->middleware('sas.role:Admin');

    // API endpoints for cascade dropdowns (Staff/Admin)
    Route::get('api/sub-departments/{departmentId}', [UspkSubmissionController::class, 'getSubDepartments'])
        ->name('api.sub-departments')
        ->middleware('sas.role:Staff,Admin');
    Route::get('api/blocks/{subDepartmentId}', [UspkSubmissionController::class, 'getBlocks'])
        ->name('api.blocks')
        ->middleware('sas.role:Staff,Admin');
    Route::get('api/budget-activities', [UspkSubmissionController::class, 'getBudgetActivities'])
        ->name('api.budget-activities')
        ->middleware('sas.role:Staff,Admin');
    Route::get('api/eligible-uspks', [UspkBappController::class, 'getEligibleUspks'])
        ->name('api.eligible-uspks');

    // USPK BAPP
    Route::resource('bapp', UspkBappController::class)
        ->only(['index', 'create', 'store', 'show']);
});
