<?php

namespace Modules\SystemISPO\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\SystemISPO\App\Models\HrExternalDataRequest;

class HrDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => HrExternalDataRequest::count(),
            'pending' => HrExternalDataRequest::where('status_proses', 'menunggu')->count(),
            'processing' => HrExternalDataRequest::where('status_proses', 'diproses')->count(),
            'finished' => HrExternalDataRequest::where('status_proses', 'selesai')->count(),
        ];

        $recentRequests = HrExternalDataRequest::with('picUser')->latest()->take(5)->get();

        return view('systemispo::hr.dashboard', compact('stats', 'recentRequests'));
    }
}
