<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\SystemMaintenanceController;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class ModuleHubController extends Controller
{
    public function index(): View
    {
        $status = SystemMaintenanceController::getStatus();

        $sasReady = Route::has('sas.dashboard');
        $qcReady = Route::has('qc.dashboard');
        $hrReady = Route::has('hr.dashboard');
        $managementReady = Route::has('management.dashboard');
        $prReady = Route::has('pr.dashboard');

        $modules = [
            [
                'name' => 'Service Agreement System',
                'description' => 'Kelola kontraktor, USPK submission, dan workflow approval.',
                'route' => $sasReady ? route('sas.dashboard') : '#',
                'icon' => 'fa-screwdriver-wrench',
                'accent' => 'steel',
                'disabled' => ! $sasReady || ! ($status['ServiceAgreementSystem'] ?? true),
            ],
            [
                'name' => 'QC Complaint System',
                'description' => 'Pelaporan temuan QC, tracking penyelesaian, dan approval close.',
                'route' => $qcReady ? route('qc.dashboard') : '#',
                'icon' => 'fa-clipboard-check',
                'accent' => 'amber',
                'disabled' => ! $qcReady || ! ($status['QcComplaintSystem'] ?? true),
            ],
            [
                'name' => 'HR Modul',
                'description' => 'Sub-Sistem ISPO dan Rekapitulasi Permintaan Data Eksternal berbasis tiket.',
                'route' => $hrReady ? route('hr.dashboard') : '#',
                'icon' => 'fa-users-between-lines',
                'accent' => 'green',
                'disabled' => ! $hrReady || ! ($status['SystemISPO'] ?? true),
            ],
            [
                'name' => 'User & Role Management',
                'description' => 'Kelola user, role, master organisasi, dan log aktivitas lintas modul.',
                'route' => $managementReady ? route('management.dashboard') : '#',
                'icon' => 'fa-users-gear',
                'accent' => 'steel',
                'disabled' => ! $managementReady || ! ($status['management'] ?? true),
            ],
            [
                'name' => 'Purchase Request System',
                'description' => 'Sistem Purchase Request, PO, dan Request Capex.',
                'route' => $prReady ? route('pr.dashboard') : '#',
                'icon' => 'fa-shopping-cart',
                'accent' => 'amber',
                'disabled' => ! $prReady || ! ($status['PrSystem'] ?? true),
            ],
        ];

        $systemSupportReady = Route::has('systemsupport.dashboard');

        $modules[] = [
            'name' => 'System Support',
            'description' => 'Helpdesk IT untuk ticketing, pengumuman sistem, dan tindak lanjut support.',
            'route' => $systemSupportReady ? route('systemsupport.dashboard') : '#',
            'icon' => 'fa-headset',
            'accent' => 'steel',
            'disabled' => ! $systemSupportReady || ! ($status['SystemSupport'] ?? true),
        ];

        return view('modules.index', [
            'modules' => $modules,
        ]);
    }
}
