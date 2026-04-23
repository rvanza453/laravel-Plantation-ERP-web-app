<?php

namespace Modules\ServiceAgreementSystem\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Modules\ServiceAgreementSystem\Models\UspkSubmission;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'total_uspk' => UspkSubmission::count(),
            'draft' => UspkSubmission::where('status', 'draft')->count(),
            'submitted' => UspkSubmission::where('status', 'submitted')->count(),
            'in_review' => UspkSubmission::where('status', 'in_review')->count(),
            'approved' => UspkSubmission::where('status', 'approved')->count(),
            'rejected' => UspkSubmission::where('status', 'rejected')->count(),
        ];

        $recentUspk = UspkSubmission::with(['department', 'submitter'])
            ->latest()
            ->limit(5)
            ->get();

        // 1. USPK per Afdeling
        $uspkByDepartment = UspkSubmission::select('department_id', \DB::raw('count(*) as total'))
            ->with('department')
            ->groupBy('department_id')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->department->name ?? 'Tanpa Departemen',
                    'total' => $item->total
                ];
            });

        // 2. Status Distribusi USPK
        $uspkByStatus = UspkSubmission::select('status', \DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();
        
        // Label Translasi
        $statusMap = [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'in_review' => 'In Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
        $mappedStatusData = [];
        foreach($statusMap as $key => $label) {
            if(isset($uspkByStatus[$key])) {
                $mappedStatusData[] = ['name' => $label, 'total' => $uspkByStatus[$key]];
            }
        }

        // 3. Top Contractors
        $topContractors = \Modules\ServiceAgreementSystem\Models\Contractor::withCount(['tenders' => function($q) {
                $q->where('is_selected', true);
            }])
            ->having('tenders_count', '>', 0)
            ->orderByDesc('tenders_count')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'name' => trim(str_replace(['CV.', 'PT.', 'CV', 'PT'], '', $item->name)),
                    'total' => $item->tenders_count
                ];
            });

        // 4. Late Contractors (Mangkir)
        $lateContractorsStats = [];
        $hasWorkReportedCompletedAt = Schema::hasColumn('uspk_submissions', 'work_reported_completed_at');
        $hasLegalSpkUploadedAt = Schema::hasColumn('uspk_submissions', 'legal_spk_uploaded_at');

        $completedTenders = ($hasWorkReportedCompletedAt && $hasLegalSpkUploadedAt)
            ? \Modules\ServiceAgreementSystem\Models\UspkTender::where('is_selected', true)
                ->whereHas('submission', function($q) {
                    $q->whereNotNull('work_reported_completed_at')
                      ->whereNotNull('legal_spk_uploaded_at');
                })
                ->with(['submission', 'contractor'])
                ->get()
            : collect();
            
        foreach($completedTenders as $tender) {
            $durationString = $tender->tender_duration;
            $plannedDays = (int) filter_var($durationString, FILTER_SANITIZE_NUMBER_INT);
            if($plannedDays > 0) {
                $start = \Carbon\Carbon::parse($tender->submission->legal_spk_uploaded_at);
                $end = \Carbon\Carbon::parse($tender->submission->work_reported_completed_at);
                $actualDays = $start->diffInDays($end);
                
                if($actualDays > $plannedDays) {
                    $cName = trim(str_replace(['CV.', 'PT.', 'CV', 'PT'], '', $tender->contractor->name ?? 'Unknown'));
                    if(!isset($lateContractorsStats[$cName])) {
                        $lateContractorsStats[$cName] = 0;
                    }
                    $lateContractorsStats[$cName]++;
                }
            }
        }
        
        arsort($lateContractorsStats);
        $lateContractorsStats = array_slice($lateContractorsStats, 0, 5);
        
        $lateContractors = [];
        foreach($lateContractorsStats as $name => $count) {
            $lateContractors[] = ['name' => $name, 'total' => $count];
        }

        return view('serviceagreementsystem::dashboard', compact(
            'stats', 
            'recentUspk',
            'uspkByDepartment',
            'mappedStatusData',
            'topContractors',
            'lateContractors'
        ));
    }
}
