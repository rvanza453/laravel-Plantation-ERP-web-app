<?php

use Illuminate\Support\Facades\Route;
use Modules\LabSystem\Http\Controllers\LabSystemController;
use Modules\LabSystem\Http\Controllers\LabVerifierConfigController;

Route::middleware(['web', 'auth', 'assigned.role'])->prefix('lab')->name('lab.')->group(function () {
    Route::get('/', [LabSystemController::class, 'index'])->name('dashboard');
    Route::get('/sampling', [LabSystemController::class, 'samplingForm'])->name('sampling.form');
    Route::get('/sampling/create', [LabSystemController::class, 'samplingForm'])->name('sampling.create');
    Route::post('/sampling', [LabSystemController::class, 'storeSampling'])->name('sampling.store');
    Route::post('/sampling/end-shift', [LabSystemController::class, 'endShift'])->name('sampling.end-shift');
    Route::get('/sampling/verifier', [LabSystemController::class, 'verifierInbox'])->name('sampling.verifier');
    Route::get('/sampling/verifier/{batch}', [LabSystemController::class, 'verifierDetail'])->name('sampling.verifier.detail');
    Route::post('/sampling/{batch}/approve', [LabSystemController::class, 'approveSampling'])->name('sampling.approve');
    Route::post('/sampling/{batch}/reject', [LabSystemController::class, 'rejectSampling'])->name('sampling.reject');
    Route::get('/report/quality-daily', [LabSystemController::class, 'qualityReport'])->name('report.quality-daily');
    
    // Config Management (Admin/Supervisor only)
    Route::get('/config/verifiers', [LabVerifierConfigController::class, 'index'])->name('config.verifiers');
    Route::get('/config/verifiers/create', [LabVerifierConfigController::class, 'create'])->name('config.verifiers.create');
    Route::post('/config/verifiers', [LabVerifierConfigController::class, 'store'])->name('config.verifiers.store');
    Route::get('/config/verifiers/{assignment}/edit', [LabVerifierConfigController::class, 'edit'])->name('config.verifiers.edit');
    Route::put('/config/verifiers/{assignment}', [LabVerifierConfigController::class, 'update'])->name('config.verifiers.update');
    Route::delete('/config/verifiers/{assignment}', [LabVerifierConfigController::class, 'destroy'])->name('config.verifiers.destroy');
    Route::get('/api/assigned-verifiers', [LabVerifierConfigController::class, 'getAssignedVerifiers'])->name('api.assigned-verifiers');
});
