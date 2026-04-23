<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemISPO\Http\Controllers\SystemISPOController;
use Modules\SystemISPO\Http\Controllers\IspoController;
use Modules\SystemISPO\Http\Controllers\AdminIspoItemController;
use Modules\SystemISPO\Http\Controllers\HrDashboardController;
use Modules\SystemISPO\Http\Controllers\HrExternalDataRequestController;
use Modules\SystemISPO\Http\Controllers\HrExternalDataRequestPublicController;

Route::middleware(['auth', 'assigned.role', 'ispo.role'])->group(function () {
    Route::resource('systemispos', SystemISPOController::class)->names('systemispo');

    Route::prefix('hr')->name('hr.')->group(function () {
        Route::get('/', [HrDashboardController::class, 'index'])->name('dashboard');

        Route::resource('external-requests', HrExternalDataRequestController::class)
            ->parameters(['external-requests' => 'externalRequest'])
            ->except(['destroy'])
            ->names('external-requests');

        Route::delete(
            'external-requests/{externalRequest}/attachments/{attachment}',
            [HrExternalDataRequestController::class, 'destroyAttachment']
        )->name('external-requests.attachments.destroy');

        Route::get(
            'external-requests/{externalRequest}/attachments/{attachment}/preview',
            [HrExternalDataRequestController::class, 'previewAttachment']
        )->name('external-requests.attachments.preview');

        Route::get(
            'external-requests/{externalRequest}/attachments/{attachment}/download',
            [HrExternalDataRequestController::class, 'downloadAttachment']
        )->name('external-requests.attachments.download');

        Route::post(
            'external-requests/{externalRequest}/share-token',
            [HrExternalDataRequestController::class, 'generateShareToken']
        )->name('external-requests.share.generate');

        Route::patch(
            'external-requests/{externalRequest}/share-token/{token}/revoke',
            [HrExternalDataRequestController::class, 'revokeShareToken']
        )->name('external-requests.share.revoke');
    });
    
    Route::prefix('ispo')->name('ispo.')->group(function () {
        Route::get('/', [IspoController::class, 'index'])->name('index');
        Route::post('/store', [IspoController::class, 'store'])->name('store');
        Route::get('/{id}', [IspoController::class, 'show'])->name('show');
        Route::post('/{id}/entry', [IspoController::class, 'updateEntry'])->name('updateEntry');
        Route::post('/{id}/bulk-update', [IspoController::class, 'bulkUpdate'])->name('bulkUpdate');
        Route::delete('/attachment/{id}', [IspoController::class, 'destroyAttachment'])->name('attachment.destroy');
        Route::get('/history/{entryId}', [IspoController::class, 'getHistory'])->name('history');

        // Admin Routes - Only for Admin
        Route::middleware(['ispo.role:HR Admin,ISPO Admin'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('/items', [AdminIspoItemController::class, 'index'])->name('items.index');
            Route::post('/items', [AdminIspoItemController::class, 'store'])->name('items.store');
            Route::put('/items/{item}', [AdminIspoItemController::class, 'update'])->name('items.update');
            Route::delete('/items/{item}', [AdminIspoItemController::class, 'destroy'])->name('items.destroy');
        });
    });
});

Route::prefix('hr/external-requests/public')
    ->name('hr.external-requests.public.')
    ->middleware(['web', 'throttle:30,1'])
    ->group(function () {
        Route::get('/{token}', [HrExternalDataRequestPublicController::class, 'show'])->name('show');
        Route::get('/{token}/preview/{attachment}', [HrExternalDataRequestPublicController::class, 'preview'])->name('preview');
        Route::get('/{token}/download/{attachment}', [HrExternalDataRequestPublicController::class, 'download'])->name('download');
    });
