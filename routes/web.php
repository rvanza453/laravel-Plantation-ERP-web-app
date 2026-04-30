<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GlobalManagementController;
use App\Http\Controllers\ModuleHubController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('modules.index')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    // Backward-compatible SAS login aliases used by legacy views.
    Route::get('/sas/login', [LoginController::class, 'showLoginForm'])->name('sas.login');
    Route::post('/sas/login', [LoginController::class, 'login'])->name('sas.login.submit');
});

Route::middleware(['auth', 'assigned.role'])->group(function () {
    Route::get('/modules', [ModuleHubController::class, 'index'])->name('modules.index');

    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/management', [GlobalManagementController::class, 'index'])->name('management.dashboard');

        // New Master Admin Area (Separate from Modules)
        Route::prefix('/admin')->name('admin.')->group(function () {
            Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
            Route::post('users/{user}/impersonate', [\App\Http\Controllers\Admin\UserController::class, 'impersonate'])->name('users.impersonate');
            Route::post('users/leave-impersonate', [\App\Http\Controllers\Admin\UserController::class, 'leaveImpersonate'])->name('users.leave-impersonate');
            Route::resource('sites', \App\Http\Controllers\Admin\SiteController::class);
            Route::resource('master-departments', \App\Http\Controllers\Admin\MasterDepartmentController::class);
            Route::resource('departments', \App\Http\Controllers\Admin\DepartmentController::class);
            Route::resource('sub-departments', \App\Http\Controllers\Admin\SubDepartmentController::class);
                Route::resource('blocks', \App\Http\Controllers\Admin\BlockController::class);
            Route::resource('activity-logs', \App\Http\Controllers\Admin\ActivityLogController::class, ['only' => ['index']]);
            
            // API route for fetching departments by site for User form
            Route::get('/api/sites/{site}/departments', function (\Modules\ServiceAgreementSystem\Models\Site $site) {
                return response()->json($site->departments()->select('id', 'name')->orderBy('name')->get());
            })->name('api.sites.departments');
            
            // Maintenance Routes
            Route::get('maintenance', [\App\Http\Controllers\Admin\SystemMaintenanceController::class, 'index'])->name('maintenance.index');
            Route::post('maintenance/{module}', [\App\Http\Controllers\Admin\SystemMaintenanceController::class, 'toggle'])->name('maintenance.toggle');
            Route::post('maintenance/tools', [\App\Http\Controllers\Admin\SystemMaintenanceController::class, 'runTool'])->name('maintenance.tools');
        });

        // Backward-compatible aliases to keep old account URLs working,
        // while user management is now centralized at admin.users.* routes.
        Route::get('/accounts', fn () => redirect()->route('admin.users.index'))->name('accounts.index');
        Route::get('/accounts/create', fn () => redirect()->route('admin.users.create'))->name('accounts.create');
        Route::get('/accounts/{account}/edit', fn (User $account) => redirect()->route('admin.users.edit', $account))->name('accounts.edit');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    // Global profile routes (outside module-specific profile pages)
    Route::get('/global-profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('global.profile.show');
    Route::get('/global-profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('global.profile.edit');
    Route::put('/global-profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('global.profile.update');
    Route::post('/global-profile/signature', [\App\Http\Controllers\ProfileController::class, 'uploadSignature'])->name('global.profile.signature.upload');
    Route::delete('/global-profile/signature', [\App\Http\Controllers\ProfileController::class, 'deleteSignature'])->name('global.profile.signature.delete');

    // Backward-compatible SAS logout alias used by legacy layouts.
    Route::post('/sas/logout', [LoginController::class, 'logout'])->name('sas.logout');
});

Route::get('/run-one-time-migration', [App\Http\Controllers\OneTimeMigrationController::class, 'runJobNormalization']);