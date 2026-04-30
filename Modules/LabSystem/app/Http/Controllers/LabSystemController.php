<?php

namespace Modules\LabSystem\Http\Controllers;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Dompdf\Dompdf;
use Dompdf\Options;

class LabSystemController extends Controller
{
    public function index()
    {
        $stats = [
            'sampling_batches' => Schema::hasTable('lab_sampling_batches')
                ? DB::table('lab_sampling_batches')->count()
                : 0,
            'pending_analyses' => Schema::hasTable('lab_analysis_reports')
                ? DB::table('lab_analysis_reports')->where('status', 'draft')->count()
                : 0,
            'published_reports' => Schema::hasTable('lab_analysis_reports')
                ? DB::table('lab_analysis_reports')->whereNotNull('published_at')->count()
                : 0,
            'measurement_points' => Schema::hasTable('lab_sampling_measurements')
                ? DB::table('lab_sampling_measurements')->count()
                : 0,
            'approved_sampling_reports' => Schema::hasTable('lab_sampling_batches')
                ? DB::table('lab_sampling_batches')->where('status', 'approved')->count()
                : 0,
        ];

        return view('labsystem::index', compact('stats'));
    }

    public function samplingForm()
    {
        $parameters = collect();
        $todayEntries = collect();
        $defaultSourceUnit = null;
        $activeShift = now()->hour >= 7 && now()->hour < 19 ? 1 : 2;

        if (Schema::hasTable('lab_sampling_parameters')) {
            $parameters = DB::table('lab_sampling_parameters')
                ->where('is_active', true)
                ->orderBy('display_order')
                ->orderBy('id')
                ->get();
        }

        if (
            auth()->check()
            && Schema::hasTable('lab_sampling_batches')
            && Schema::hasTable('lab_sampling_measurements')
            && Schema::hasTable('lab_sampling_parameters')
        ) {
            $today = now()->toDateString();
            $userId = (int) auth()->id();

            $todayEntries = DB::table('lab_sampling_measurements as m')
                ->join('lab_sampling_batches as b', 'b.id', '=', 'm.lab_sampling_batch_id')
                ->join('lab_sampling_parameters as p', 'p.id', '=', 'm.lab_sampling_parameter_id')
                ->whereDate('b.sampling_date', $today)
                ->where('b.sampler_user_id', $userId)
                ->orderByDesc('m.measured_at')
                ->orderByDesc('m.id')
                ->select([
                    'm.id',
                    'p.id as parameter_id',
                    'm.measured_value',
                    'm.measured_text',
                    'm.measured_at',
                    'b.source_unit',
                    'b.shift',
                    'b.notes as batch_notes',
                    'p.category',
                    'p.parameter_name',
                    'p.unit',
                    'p.standard_text',
                    'p.sampling_frequency',
                ])
                ->get();

            $latestBatch = DB::table('lab_sampling_batches')
                ->whereDate('sampling_date', $today)
                ->where('sampler_user_id', $userId)
                ->orderByDesc('submitted_at')
                ->orderByDesc('id')
                ->first(['source_unit', 'shift']);

            if ($latestBatch) {
                $defaultSourceUnit = $latestBatch->source_unit;
                $activeShift = (int) ($latestBatch->shift ?? $activeShift);
            }

            // For now, force default source unit to PKS SSM for all new inputs
            $defaultSourceUnit = 'PKS SSM';
        }

        [$missionGroups, $missionTotal, $missionCompleted, $missionPercent] = $this->buildMissionChecklist($parameters, $todayEntries);
        [$questCompleted, $questTotal] = $this->buildQuestProgressSummary($parameters, $todayEntries);

        return view('labsystem::sampling.form', [
            'parameters' => $parameters,
            'todayEntries' => $todayEntries,
            'defaultSourceUnit' => $defaultSourceUnit,
            'activeShift' => $activeShift,
            'missionGroups' => $missionGroups,
            'missionTotal' => $missionTotal,
            'missionCompleted' => $missionCompleted,
            'missionPercent' => $missionPercent,
            'questCompleted' => $questCompleted,
            'questTotal' => $questTotal,
        ]);
    }

    public function storeSampling(Request $request)
    {
        $validated = $request->validate([
            'source_unit' => ['required', 'string', 'max:100'],
            'shift' => ['nullable', 'integer', 'in:1,2'],
            'notes' => ['nullable', 'string'],
            'parameter_id' => ['nullable', 'integer', 'exists:lab_sampling_parameters,id'],
            'measured_value' => ['nullable', 'string', 'max:50'],
            'measured_time' => ['nullable', 'date_format:H:i'],
            'measurements' => ['nullable', 'array'],
            'measurements.*' => ['nullable', 'string', 'max:50'],
        ]);

        $userId = auth()->id();
        abort_unless($userId, 403, 'Petugas sampling tidak terautentikasi.');

        abort_unless(
            Schema::hasTable('lab_sampling_batches')
            && Schema::hasTable('lab_sampling_measurements')
            && Schema::hasTable('lab_sampling_parameters'),
            500,
            'Tabel sampling belum tersedia. Jalankan migration modul Lab terlebih dahulu.'
        );

        // Get or create today's daily session
        $today = now()->toDateString();
        $dailySessionId = null;

        if (Schema::hasTable('lab_daily_sessions')) {
            $session = DB::table('lab_daily_sessions')
                ->where('session_date', $today)
                ->where('status', 'open')
                ->first(['id']);

            if (!$session) {
                $dailySessionId = DB::table('lab_daily_sessions')->insertGetId([
                    'session_date' => $today,
                    'status' => 'open',
                    'started_by' => $userId,
                    'started_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $dailySessionId = $session->id;
            }
        }

        $submittedAt = now();
        $singleInputMode = !empty($validated['parameter_id']) || trim((string) ($validated['measured_value'] ?? '')) !== '';

        if ($singleInputMode) {
            $parameterId = (int) ($validated['parameter_id'] ?? 0);
            $rawValue = trim((string) ($validated['measured_value'] ?? ''));

            if ($parameterId <= 0 || $rawValue === '') {
                return back()
                    ->withInput()
                    ->withErrors(['measured_value' => 'Pilih komponen dan isi nilai pengukuran terlebih dahulu.']);
            }

            $measuredAt = $submittedAt;
            if (!empty($validated['measured_time'])) {
                $measuredAt = now()->setTimeFromTimeString($validated['measured_time']);
            }

            $normalizedNumeric = str_replace(',', '.', $rawValue);
            $numericValue = is_numeric($normalizedNumeric) ? (float) $normalizedNumeric : null;

            DB::transaction(function () use ($validated, $submittedAt, $measuredAt, $parameterId, $rawValue, $numericValue, $userId, $dailySessionId): void {
                $batchId = DB::table('lab_sampling_batches')->insertGetId([
                    'batch_code' => sprintf('LAB-%s-%04d', $submittedAt->format('YmdHis'), random_int(1, 9999)),
                    'sampling_date' => $submittedAt->toDateString(),
                    'source_unit' => $validated['source_unit'],
                    'sampler_user_id' => $userId,
                    'status' => 'draft',
                    'notes' => $validated['notes'] ?? null,
                    'shift' => (int) ($validated['shift'] ?? 1),
                    'submitted_at' => $submittedAt,
                    'created_at' => $submittedAt,
                    'updated_at' => $submittedAt,
                ]);

                $measurementData = [
                    'lab_sampling_batch_id' => $batchId,
                    'lab_sampling_parameter_id' => $parameterId,
                    'measured_value' => $numericValue,
                    'measured_text' => $rawValue,
                    'analysis_method' => 'sampling_input_mobile',
                    'measured_at' => $measuredAt,
                    'analyst_user_id' => $userId,
                    'created_at' => $submittedAt,
                    'updated_at' => $submittedAt,
                ];

                if (Schema::hasColumn('lab_sampling_measurements', 'created_by')) {
                    $measurementData['created_by'] = $userId;
                }

                if ($dailySessionId) {
                    if (Schema::hasColumn('lab_sampling_measurements', 'daily_session_id')) {
                        $measurementData['daily_session_id'] = $dailySessionId;
                    }
                }

                DB::table('lab_sampling_measurements')->insert($measurementData);
            });

            return redirect()
                ->route('lab.sampling.form')
                ->with('success', 'Data komponen tersimpan sebagai draft. Klik Tutup Sesi Harian untuk kirim ke verifikator.');
        }

        $measurements = collect($validated['measurements'] ?? []);

        DB::transaction(function () use ($validated, $submittedAt, $measurements, $userId, $dailySessionId): void {
            $batchId = DB::table('lab_sampling_batches')->insertGetId([
                'batch_code' => sprintf('LAB-%s-%04d', $submittedAt->format('YmdHis'), random_int(1, 9999)),
                'sampling_date' => $submittedAt->toDateString(),
                'source_unit' => $validated['source_unit'],
                'sampler_user_id' => $userId,
                'status' => 'draft',
                'notes' => $validated['notes'] ?? null,
                'shift' => (int) ($validated['shift'] ?? 1),
                'submitted_at' => $submittedAt,
                'created_at' => $submittedAt,
                'updated_at' => $submittedAt,
            ]);

            $parameterIds = DB::table('lab_sampling_parameters')->pluck('id')->map(fn ($id) => (int) $id)->all();

            $rows = [];
            foreach ($parameterIds as $parameterId) {
                $rawValue = trim((string) ($measurements->get((string) $parameterId, '')));
                if ($rawValue === '') {
                    continue;
                }

                $normalizedNumeric = str_replace(',', '.', $rawValue);
                $numericValue = is_numeric($normalizedNumeric) ? (float) $normalizedNumeric : null;

                $row = [
                    'lab_sampling_batch_id' => $batchId,
                    'lab_sampling_parameter_id' => $parameterId,
                    'measured_value' => $numericValue,
                    'measured_text' => $rawValue,
                    'analysis_method' => 'sampling_input_mobile',
                    'measured_at' => $submittedAt,
                    'analyst_user_id' => $userId,
                    'created_at' => $submittedAt,
                    'updated_at' => $submittedAt,
                ];

                if (Schema::hasColumn('lab_sampling_measurements', 'created_by')) {
                    $row['created_by'] = $userId;
                }

                if ($dailySessionId) {
                    if (Schema::hasColumn('lab_sampling_measurements', 'daily_session_id')) {
                        $row['daily_session_id'] = $dailySessionId;
                    }
                }

                $rows[] = $row;
            }

            if (!empty($rows)) {
                DB::table('lab_sampling_measurements')->insert($rows);
            }
        });

        return redirect()
            ->route('lab.sampling.form')
            ->with('success', 'Data sampling tersimpan sebagai draft dengan waktu submit sistem.');
    }

    /**
     * Close the daily session and mark all batches as pending verification.
     */
    public function closeDailySession(Request $request)
    {
        $user = auth()->user();
        abort_unless($user, 403, 'User tidak terautentikasi.');

        $today = now()->toDateString();

        // Mark today's session as pending verification
        if (Schema::hasTable('lab_daily_sessions')) {
            DB::table('lab_daily_sessions')
                ->where('session_date', $today)
                ->where('status', 'open')
                ->update([
                    'status' => 'pending_verification',
                    'closed_by' => $user->id,
                    'closed_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        // Mark all draft and rejected batches from this session as pending
        if (Schema::hasTable('lab_sampling_batches')) {
            DB::table('lab_sampling_batches')
                ->whereDate('sampling_date', $today)
                ->where('sampler_user_id', $user->id)
                ->whereIn('status', ['draft', 'rejected'])
                ->update([
                    'status' => 'pending',
                    'updated_at' => now(),
                ]);
        }

        // Optionally store session summary if shift_sessions table exists
        $forcePenalty = (bool) ($request->input('force_penalty', false));
        $activeShift = now()->hour >= 7 && now()->hour < 19 ? 1 : 2;

        [$missionGroups, $missionTotal, $missionCompleted, $missionPercent] = $this->buildMissionChecklist(
            collect(),
            collect()
        );

        $shiftMissions = collect($missionGroups ?? [])->get('Target Per 2 Jam', collect());
        $dailyMissions = collect($missionGroups ?? [])->get('Target Harian', collect());

        $shiftCompleted = $shiftMissions->filter(fn ($m) => $m->mission_completed ?? false)->count();
        $dailyCompleted = $dailyMissions->filter(fn ($m) => $m->mission_completed ?? false)->count();
        $dailyTotal = $dailyMissions->count();

        $isDailyComplete = $dailyCompleted >= $dailyTotal;
        $shiftMissionsTotal = $shiftMissions->count();
        $finalScore = $shiftMissionsTotal > 0 
            ? (int) round(($shiftCompleted / $shiftMissionsTotal) * 100)
            : 0;

        $receivedPenalty = false;
        if (!$isDailyComplete && $activeShift === 2 && $forcePenalty) {
            $receivedPenalty = true;
            $finalScore = max(0, $finalScore - 20);
        }

        if (Schema::hasTable('lab_shift_sessions')) {
            DB::table('lab_shift_sessions')->insert([
                'session_date' => $today,
                'shift' => $activeShift,
                'user_id' => $user->id,
                'shift_start_at' => now()->startOfDay(),
                'shift_end_at' => now(),
                'status' => 'ended',
                'shift_missions_completed' => $shiftCompleted,
                'shift_missions_total' => $shiftMissionsTotal,
                'daily_missions_completed' => $dailyCompleted,
                'daily_missions_total' => $dailyTotal,
                'daily_mission_completed_by_this_shift' => $isDailyComplete,
                'received_penalty' => $receivedPenalty,
                'final_score_percent' => $finalScore,
                'is_mvp' => $isDailyComplete,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sesi harian ditutup dan data dikirim ke verifikator.',
            'final_score' => $finalScore,
        ]);
    }

    /**
     * Keep endShift for backward compatibility, delegate to closeDailySession
     */
    public function endShift(Request $request)
    {
        return $this->closeDailySession($request);
    }

    /**
     * Build the daily mission checklist with reset timing.
     *
     * @return array{0: array<string, Collection>, 1: int, 2: int, 3: int}
     */
    private function buildMissionChecklist(Collection $parameters, Collection $todayEntries): array
    {
        $now = now();
        $windowStart = $this->currentTwoHourWindowStart($now);
        $windowEnd = $windowStart->copy()->addHours(2);
        $dailyResetAt = $now->copy()->addDay()->startOfDay();
        $entriesByParameter = $todayEntries->groupBy('parameter_id');

        $missionGroups = [
            'Target Per 2 Jam' => collect(),
            'Target Harian' => collect(),
        ];

        $missionParameters = $parameters->filter(function ($parameter): bool {
            return ! str_contains(strtolower((string) ($parameter->parameter_name ?? '')), 'total');
        });

        foreach ($missionParameters as $parameter) {
            $samplingFrequency = strtolower((string) ($parameter->sampling_frequency ?? ''));
            $isTwoHour = str_contains($samplingFrequency, '2 jam');
            $parameterEntries = $entriesByParameter->get((int) $parameter->id, collect());

            $completedEntry = $isTwoHour
                ? $parameterEntries->first(function ($entry) use ($windowStart, $windowEnd): bool {
                    if (empty($entry->measured_at)) {
                        return false;
                    }

                    $measuredAt = Carbon::parse($entry->measured_at);

                    return $measuredAt->greaterThanOrEqualTo($windowStart) && $measuredAt->lt($windowEnd);
                })
                : $parameterEntries->first();

            $missionItem = clone $parameter;
            $missionItem->mission_completed = (bool) $completedEntry;
            $missionItem->mission_last_measured_at = $completedEntry->measured_at ?? null;
            $missionItem->mission_reset_at = $isTwoHour ? $windowEnd->copy() : $dailyResetAt->copy();
            $missionItem->mission_reset_label = $isTwoHour ? $windowEnd->format('H:i') : 'Besok 00:00';
            $missionItem->mission_reset_iso = $missionItem->mission_reset_at->toIso8601String();
            $missionItem->mission_window_label = $isTwoHour
                ? $windowStart->format('H:i') . ' - ' . $windowEnd->format('H:i')
                : $now->translatedFormat('d M Y');

            $groupKey = $isTwoHour ? 'Target Per 2 Jam' : 'Target Harian';
            $missionGroups[$groupKey]->push($missionItem);
        }

        $missionTotal = collect($missionGroups)->flatten(1)->count();
        $missionCompleted = collect($missionGroups)->flatten(1)->filter(fn ($item) => (bool) ($item->mission_completed ?? false))->count();
        $missionPercent = $missionTotal > 0 ? (int) round(($missionCompleted / $missionTotal) * 100) : 0;

        return [$missionGroups, $missionTotal, $missionCompleted, $missionPercent];
    }

    private function currentTwoHourWindowStart(Carbon $time): Carbon
    {
        $windowStart = $time->copy()->startOfHour();
        $offset = $windowStart->hour % 2;

        if ($offset > 0) {
            $windowStart->subHours($offset);
        }

        return $windowStart;
    }

    /**
     * Get the time when first data entry was made today.
     * Quest counting starts from this time, not from midnight.
     */
    private function getFirstEntryTime(Collection $todayEntries): ?Carbon
    {
        if ($todayEntries->isEmpty()) {
            return null;
        }

        $firstEntry = $todayEntries
            ->sortBy('measured_at')
            ->first();

        return $firstEntry ? Carbon::parse($firstEntry->measured_at) : null;
    }

    /**
     * Build cumulative quest progress so completed 2-hour quests stay counted
     * when a new window starts. Quest counting STARTS from first data entry time.
     */
    private function buildQuestProgressSummary(Collection $parameters, Collection $todayEntries): array
    {
        $now = now();
        
        // Quest only starts counting after first data entry
        $firstEntryTime = $this->getFirstEntryTime($todayEntries);
        if (!$firstEntryTime) {
            // No entries yet, so no quest progress
            return [0, 0];
        }

        // Start quest calculation from the 2-hour window containing first entry
        $questStartWindow = $this->currentTwoHourWindowStart($firstEntryTime);
        $currentWindowEnd = $this->currentTwoHourWindowStart($now)->copy()->addHours(2);
        $entriesByParameter = $todayEntries->groupBy('parameter_id');

        $shiftParameters = $parameters->filter(function ($parameter): bool {
            return str_contains(strtolower((string) ($parameter->sampling_frequency ?? '')), '2 jam');
        });

        $dailyParameters = $parameters->filter(function ($parameter): bool {
            return ! str_contains(strtolower((string) ($parameter->sampling_frequency ?? '')), '2 jam');
        });

        $questCompleted = 0;
        $questTotal = 0;

        // Loop through all 2-hour windows from first entry to current time
        for ($windowCursor = $questStartWindow->copy(); $windowCursor->lt($currentWindowEnd); $windowCursor->addHours(2)) {
            $windowStart = $windowCursor->copy();
            $windowEnd = $windowCursor->copy()->addHours(2);

            foreach ($shiftParameters as $parameter) {
                $questTotal++;

                $parameterEntries = $entriesByParameter->get((int) $parameter->id, collect());
                $hasEntryInWindow = $parameterEntries->contains(function ($entry) use ($windowStart, $windowEnd): bool {
                    if (empty($entry->measured_at)) {
                        return false;
                    }

                    $measuredAt = Carbon::parse($entry->measured_at);

                    return $measuredAt->greaterThanOrEqualTo($windowStart) && $measuredAt->lt($windowEnd);
                });

                if ($hasEntryInWindow) {
                    $questCompleted++;
                }
            }
        }

        // Daily quests count only once if any entry exists
        foreach ($dailyParameters as $parameter) {
            $questTotal++;

            if (($entriesByParameter->get((int) $parameter->id, collect()))->isNotEmpty()) {
                $questCompleted++;
            }
        }

        return [$questCompleted, $questTotal];
    }

    /**
     * Verifier Inbox - List all pending batches untuk diverifikasi (inbox style)
     */
    public function verifierInbox(Request $request)
    {
        abort_unless(
            Schema::hasTable('lab_sampling_batches')
            && Schema::hasTable('lab_sampling_measurements')
            && Schema::hasTable('lab_sampling_parameters'),
            500,
            'Tabel sampling belum tersedia. Jalankan migration modul Lab terlebih dahulu.'
        );

        // Filter by site, shift, date range if provided
        $siteFilter = $request->input('site_id');
        $shiftFilter = $request->input('shift');
        $dateFrom = $request->input('date_from') ? Carbon::createFromFormat('Y-m-d', $request->input('date_from'))->startOfDay() : now()->subDays(7);
        $dateTo = $request->input('date_to') ? Carbon::createFromFormat('Y-m-d', $request->input('date_to'))->endOfDay() : now()->endOfDay();

        $hasDeptColumn = Schema::hasColumn('lab_sampling_batches', 'department_id');

        $query = DB::table('lab_sampling_batches as b')
            ->leftJoin('users as sampler', 'sampler.id', '=', 'b.sampler_user_id');

        if ($hasDeptColumn) {
            $query->leftJoin('departments as d', 'd.id', '=', 'b.department_id');
        }

        $query->where('b.status', 'pending')
            ->whereBetween('b.sampling_date', [$dateFrom->toDateString(), $dateTo->toDateString()]);

        if ($siteFilter && $hasDeptColumn) {
            $query->where('d.site_id', $siteFilter);
        }

        if ($shiftFilter) {
            $query->where('b.shift', $shiftFilter);
        }

        $batches = $query->orderByDesc('b.submitted_at')
            ->select(['b.id', 'b.batch_code', 'b.sampling_date', 'b.shift', 'b.source_unit', 'b.submitted_at', 'sampler.name as sampler_name'])
            ->paginate(10);

        // Count abnormalities for each batch
        $batches->getCollection()->transform(function ($batch) {
            $abnormalCount = DB::table('lab_sampling_measurements as m')
                ->join('lab_sampling_parameters as p', 'p.id', '=', 'm.lab_sampling_parameter_id')
                ->where('m.lab_sampling_batch_id', $batch->id)
                ->get()
                ->filter(function ($row) {
                    $measured = $row->measured_value ?? '-';
                    $eval = $this->evaluateStandard($row->standard_text, $measured);
                    return $eval['is_abnormal'];
                })
                ->count();

            $batch->abnormal_count = $abnormalCount;
            return $batch;
        });

        $sites = DB::table('departments as d')
            ->join('sites as s', 's.id', '=', 'd.site_id')
            ->distinct()
            ->pluck('s.name', 's.id');

        return view('labsystem::sampling.verifier-inbox', compact(
            'batches',
            'sites',
            'siteFilter',
            'shiftFilter',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Verifier Detail - Show detail untuk verifikasi specific batch
     */
    public function verifierDetail($batchId)
    {
        abort_unless(
            Schema::hasTable('lab_sampling_batches')
            && Schema::hasTable('lab_sampling_measurements')
            && Schema::hasTable('lab_sampling_parameters'),
            500,
            'Tabel sampling belum tersedia.'
        );

        $batch = DB::table('lab_sampling_batches as b')
            ->leftJoin('users as sampler', 'sampler.id', '=', 'b.sampler_user_id')
            ->where('b.id', $batchId)
            ->where('b.status', 'pending')
            ->select(['b.*', 'sampler.name as sampler_name'])
            ->first();

        abort_if(!$batch, 404, 'Batch tidak ditemukan atau sudah diproses.');

        $rows = DB::table('lab_sampling_measurements as m')
            ->join('lab_sampling_parameters as p', 'p.id', '=', 'm.lab_sampling_parameter_id')
            ->where('m.lab_sampling_batch_id', $batchId)
            ->orderBy('p.category')
            ->orderBy('p.display_order')
            ->select(['p.category', 'p.parameter_name', 'p.unit', 'p.standard_text', 'm.measured_value', 'm.measured_text'])
            ->get();

        // Group and evaluate
        $groupedRows = $rows->groupBy('category')->map(function (Collection $categoryRows) {
            return $categoryRows->map(function ($row) {
                $measured = $row->measured_value ?? $row->measured_text;
                $eval = $this->evaluateStandard($row->standard_text, $measured);
                $row->is_abnormal = $eval['is_abnormal'];
                $row->status_text = $eval['status_text'];
                return $row;
            });
        });

        // Count abnormalities
        $abnormalCount = $rows->filter(function($row) {
            $measured = $row->measured_value ?? $row->measured_text;
            $eval = $this->evaluateStandard($row->standard_text, $measured);
            return $eval['is_abnormal'];
        })->count();

        return view('labsystem::sampling.verifier-detail', compact(
            'batch',
            'groupedRows',
            'abnormalCount'
        ));
    }

    public function approveSampling(Request $request, int $batchId)
    {
        $userId = auth()->id();
        abort_unless($userId, 403, 'User verifikator tidak terautentikasi.');

        $validated = $request->validate([
            'verifier_notes' => ['required', 'string'],
        ]);

        $affected = DB::table('lab_sampling_batches')
            ->where('id', $batchId)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'verified_by' => $userId,
                'verified_at' => now(),
                'verifier_notes' => $validated['verifier_notes'],
                'reject_reason' => null,
                'updated_at' => now(),
            ]);

        if ($affected === 0) {
            return back()->withErrors(['approval' => 'Laporan tidak ditemukan atau status sudah berubah.']);
        }

        return back()->with('success', 'Sampel berhasil diverifikasi dan dipublikasikan ke laporan manajemen.');
    }

    public function rejectSampling(Request $request, int $batchId)
    {
        $userId = auth()->id();
        abort_unless($userId, 403, 'User verifikator tidak terautentikasi.');

        $validated = $request->validate([
            'verifier_notes' => ['required', 'string'],
            'reject_reason' => ['required', 'string'],
        ]);

        $affected = DB::table('lab_sampling_batches')
            ->where('id', $batchId)
            ->whereIn('status', ['pending', 'in_analysis'])
            ->update([
                'status' => 'draft',
                'verified_by' => $userId,
                'verified_at' => now(),
                'verifier_notes' => $validated['verifier_notes'],
                'reject_reason' => $validated['reject_reason'],
                'updated_at' => now(),
            ]);

        if ($affected === 0) {
            return back()->withErrors(['reject' => 'Laporan tidak ditemukan atau status sudah berubah.']);
        }

        return back()->with('success', 'Sampel dikembalikan untuk revisi.');
    }

    private function evaluateStandard(?string $standardText, $measuredRaw): array
    {
        $standard = trim((string) $standardText);
        $measured = is_numeric($measuredRaw)
            ? (float) $measuredRaw
            : (is_numeric(str_replace(',', '.', (string) $measuredRaw)) ? (float) str_replace(',', '.', (string) $measuredRaw) : null);

        if ($standard === '' || $standard === '-' || $measured === null) {
            return [
                'is_abnormal' => false,
                'rule' => 'N/A',
                'status_text' => 'Tidak dapat dievaluasi otomatis',
            ];
        }

        $normalized = strtolower(str_replace(',', '.', $standard));

        if (preg_match('/([0-9]+(?:\.[0-9]+)?)\s*[-:]\s*([0-9]+(?:\.[0-9]+)?)/', $normalized, $match)) {
            $min = (float) $match[1];
            $max = (float) $match[2];
            $isAbnormal = $measured < $min || $measured > $max;

            return [
                'is_abnormal' => $isAbnormal,
                'rule' => "Rentang {$min} - {$max}",
                'status_text' => $isAbnormal ? 'Di luar rentang standar' : 'Dalam rentang standar',
            ];
        }

        if (preg_match('/^(?:max|<=|<)\s*([0-9]+(?:\.[0-9]+)?)/', $normalized, $match)) {
            $max = (float) $match[1];
            $isAbnormal = $measured > $max;

            return [
                'is_abnormal' => $isAbnormal,
                'rule' => "Maks {$max}",
                'status_text' => $isAbnormal ? 'Melebihi batas maksimum' : 'Sesuai batas maksimum',
            ];
        }

        if (preg_match('/^(?:min|>=|>)\s*([0-9]+(?:\.[0-9]+)?)/', $normalized, $match)) {
            $min = (float) $match[1];
            $isAbnormal = $measured < $min;

            return [
                'is_abnormal' => $isAbnormal,
                'rule' => "Minimal {$min}",
                'status_text' => $isAbnormal ? 'Di bawah batas minimum' : 'Sesuai batas minimum',
            ];
        }

        if (preg_match('/^([0-9]+(?:\.[0-9]+)?)$/', $normalized, $match)) {
            $target = (float) $match[1];
            $isAbnormal = abs($measured - $target) > 0.0001;

            return [
                'is_abnormal' => $isAbnormal,
                'rule' => "Target {$target}",
                'status_text' => $isAbnormal ? 'Menyimpang dari target standar' : 'Sesuai target',
            ];
        }

        return [
            'is_abnormal' => false,
            'rule' => $standard,
            'status_text' => 'Perlu verifikasi manual',
        ];
    }

    public function qualityReport(Request $request)
    {
        // Role-based access control: Allow manager, direktur, admin roles
        $allowedRoles = ['manager', 'direktur', 'admin', 'supervisor', 'quality control'];
        $userRoles = array_map('strtolower', auth()->user()?->getRoleNames()->toArray() ?? []);
        $hasAccess = count(array_intersect($userRoles, $allowedRoles)) > 0;
        
        if (!$hasAccess && !auth()->user()?->hasRole('manage-quality-reports')) {
            abort(403, 'Akses tidak diizinkan. Hanya untuk Manager, Direktur, atau Admin.');
        }

        $startDate = $request->input('start_date') ? Carbon::createFromFormat('Y-m-d', $request->input('start_date'))->startOfDay() : now()->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::createFromFormat('Y-m-d', $request->input('end_date'))->endOfDay() : now()->endOfDay();

        $samplingBatches = DB::table('lab_sampling_batches as b')
            ->whereBetween('b.sampling_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('b.sampling_date')
            ->orderBy('b.shift')
            ->get(['b.id', 'b.sampling_date', 'b.shift', 'b.status', 'b.sampler_user_id', 'b.verified_by', 'b.verifier_notes', 'b.created_at']);

        $verifiedBatches = $samplingBatches->where('status', 'approved');
        $pendingBatches = $samplingBatches->whereIn('status', ['draft', 'pending', 'in_analysis']);

        $measurements = collect();
        $samplingParameters = collect();
        $scorecard = [
            'ffa' => ['value' => null, 'status' => 'unknown', 'target' => '< 4.5%', 'samples' => 0],
            'oil_losses' => ['value' => null, 'status' => 'unknown', 'target' => '< 5%', 'samples' => 0],
            'moisture' => ['value' => null, 'status' => 'unknown', 'target' => '30-40%', 'samples' => 0],
        ];
        
        $trendData = [];  // For charts
        $rawTableData = [];  // For detailed table
        $level3Matrix = collect();
        $level3Hours = collect(range(1, 24))->map(fn ($hour) => sprintf('%02d', $hour));

        if (Schema::hasTable('lab_sampling_parameters')) {
            $samplingParameters = DB::table('lab_sampling_parameters')
                ->where('is_active', true)
                ->orderBy('category')
                ->orderBy('display_order')
                ->orderBy('id')
                ->get([
                    'id',
                    'category',
                    'parameter_name',
                    'unit',
                    'standard_text',
                    'sampling_frequency',
                    'is_calculated',
                    'display_order',
                ]);
        }

        if (Schema::hasTable('lab_sampling_measurements') && Schema::hasTable('lab_sampling_parameters')) {
            $measurements = DB::table('lab_sampling_measurements as m')
                ->join('lab_sampling_batches as b', 'b.id', '=', 'm.lab_sampling_batch_id')
                ->join('lab_sampling_parameters as p', 'p.id', '=', 'm.lab_sampling_parameter_id')
                ->leftJoin('users as sampler', 'sampler.id', '=', 'b.sampler_user_id')
                ->leftJoin('users as verifier', 'verifier.id', '=', 'b.verified_by')
                ->whereBetween('b.sampling_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->orderBy('b.sampling_date')
                ->orderBy('b.shift')
                ->orderBy('m.measured_at')
                ->select([
                    'm.id',
                    'm.lab_sampling_batch_id',
                    'm.measured_value',
                    'm.measured_text',
                    'm.measured_at',
                    'b.sampling_date',
                    'b.shift',
                    'b.status as batch_status',
                    'b.sampler_user_id',
                    'sampler.name as sampler_name',
                    'verifier.name as verifier_name',
                    'b.verifier_notes',
                    'p.id as parameter_id',
                    'p.parameter_name',
                    'p.category',
                    'p.unit',
                    'p.standard_text',
                    'p.display_order',
                ])
                ->get();

            // Calculate scorecard metrics
            $ffaMeasurements = $measurements->filter(fn($m) => stripos($m->parameter_name, 'FFA') !== false);
            if ($ffaMeasurements->count() > 0) {
                $ffaAvg = $ffaMeasurements->map(fn($m) => is_numeric($m->measured_value) ? (float)$m->measured_value : null)->filter(fn($v) => $v !== null)->avg();
                $scorecard['ffa']['value'] = round($ffaAvg, 2);
                $scorecard['ffa']['samples'] = $ffaMeasurements->count();
                // Assuming target < 4.5
                $scorecard['ffa']['status'] = $ffaAvg > 4.5 ? 'red' : ($ffaAvg > 3.5 ? 'yellow' : 'green');
            }

            $lossesMeasurements = $measurements->filter(fn($m) => stripos($m->parameter_name, 'Losses') !== false || stripos($m->parameter_name, 'Kelosohan') !== false);
            if ($lossesMeasurements->count() > 0) {
                $lossesTotal = $lossesMeasurements->map(fn($m) => is_numeric($m->measured_value) ? (float)$m->measured_value : null)->filter(fn($v) => $v !== null)->sum();
                $scorecard['oil_losses']['value'] = round($lossesTotal, 2);
                $scorecard['oil_losses']['samples'] = $lossesMeasurements->count();
                // Assuming target < 5
                $scorecard['oil_losses']['status'] = $lossesTotal > 5 ? 'red' : ($lossesTotal > 3 ? 'yellow' : 'green');
            }

            $moistureMeasurements = $measurements->filter(fn($m) => stripos($m->parameter_name, 'Moisture') !== false || stripos($m->parameter_name, 'Kelembaban') !== false);
            if ($moistureMeasurements->count() > 0) {
                $moistureAvg = $moistureMeasurements->map(fn($m) => is_numeric($m->measured_value) ? (float)$m->measured_value : null)->filter(fn($v) => $v !== null)->avg();
                $scorecard['moisture']['value'] = round($moistureAvg, 2);
                $scorecard['moisture']['samples'] = $moistureMeasurements->count();
                // Assuming target 30-40
                $scorecard['moisture']['status'] = ($moistureAvg < 30 || $moistureAvg > 40) ? 'red' : ($moistureAvg < 32 || $moistureAvg > 38 ? 'yellow' : 'green');
            }

            // Prepare trend data (hourly aggregates)
            $trendData = $measurements->groupBy(function($m) {
                return Carbon::parse($m->measured_at)->format('Y-m-d H:00');
            })->map(function($group, $hour) {
                $ffa = $group->filter(fn($m) => stripos($m->parameter_name, 'FFA') !== false)->map(fn($m) => is_numeric($m->measured_value) ? (float)$m->measured_value : null)->filter(fn($v) => $v !== null)->avg();
                $losses = $group->filter(fn($m) => stripos($m->parameter_name, 'Losses') !== false || stripos($m->parameter_name, 'Kelosohan') !== false)->map(fn($m) => is_numeric($m->measured_value) ? (float)$m->measured_value : null)->filter(fn($v) => $v !== null)->avg();
                $moisture = $group->filter(fn($m) => stripos($m->parameter_name, 'Moisture') !== false || stripos($m->parameter_name, 'Kelembaban') !== false)->map(fn($m) => is_numeric($m->measured_value) ? (float)$m->measured_value : null)->filter(fn($v) => $v !== null)->avg();
                
                return [
                    'hour' => Carbon::parse($hour)->format('H:i'),
                    'ffa' => $ffa !== null ? round($ffa, 2) : null,
                    'losses' => $losses !== null ? round($losses, 2) : null,
                    'moisture' => $moisture !== null ? round($moisture, 2) : null,
                ];
            })->values();

            // Prepare shift comparison data
            $shiftComparison = $measurements->groupBy('shift')->map(function($group, $shift) {
                $losses = $group->filter(fn($m) => stripos($m->parameter_name, 'Losses') !== false || stripos($m->parameter_name, 'Kelosohan') !== false)->map(fn($m) => is_numeric($m->measured_value) ? (float)$m->measured_value : null)->filter(fn($v) => $v !== null)->sum();
                
                return [
                    'shift' => "Shift {$shift}",
                    'oil_losses' => round($losses, 2),
                    'sample_count' => $group->count(),
                ];
            })->values();

            // Prepare raw table data
            $rawTableData = $measurements->groupBy(function($m) {
                return $m->category ?? 'Uncategorized';
            })->map(function($categoryGroup, $category) {
                return [
                    'category' => $category,
                    'rows' => $categoryGroup->map(function($m) {
                        $evaluation = $this->evaluateStandard($m->standard_text, $m->measured_value);
                        return [
                            'id' => $m->id,
                            'batch_id' => $m->lab_sampling_batch_id,
                            'sampling_date' => $m->sampling_date,
                            'shift' => $m->shift,
                            'batch_status' => $m->batch_status,
                            'parameter_name' => $m->parameter_name,
                            'measured_value' => $m->measured_value ?? $m->measured_text,
                            'unit' => $m->unit,
                            'standard_text' => $m->standard_text,
                            'status_text' => $evaluation['status_text'],
                            'is_abnormal' => $evaluation['is_abnormal'],
                            'sampler_name' => $m->sampler_name,
                            'verifier_name' => $m->verifier_name,
                            'verification_label' => $m->batch_status === 'approved' ? 'Terverifikasi' : 'Belum diverifikasi',
                            'verification_badge' => $m->batch_status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800',
                            'measured_at' => $m->measured_at,
                            'verifier_notes' => $m->verifier_notes,
                        ];
                    })->toArray(),
                ];
            })->values();

            $measurementsByDate = $measurements->groupBy(function ($measurement) {
                return Carbon::parse($measurement->measured_at)->toDateString();
            });

            $reportDates = [];
            $dateCursor = $startDate->copy()->startOfDay();
            $lastDate = $endDate->copy()->startOfDay();

            while ($dateCursor->lte($lastDate)) {
                $reportDates[] = $dateCursor->toDateString();
                $dateCursor->addDay();
            }

            foreach ($reportDates as $reportDate) {
                $dateMeasurements = $measurementsByDate->get($reportDate, collect());
                $categories = $samplingParameters->groupBy(function ($parameter) {
                    return $parameter->category ?? 'Uncategorized';
                })->map(function ($categoryParameters, $category) use ($dateMeasurements, $level3Hours) {
                    return [
                        'category' => $category,
                        'rows' => $categoryParameters->map(function ($parameter) use ($dateMeasurements, $level3Hours) {
                            $parameterMeasurements = $dateMeasurements->filter(function ($measurement) use ($parameter) {
                                return (int) ($measurement->parameter_id ?? 0) === (int) $parameter->id;
                            });

                            $cells = $level3Hours->map(function ($hour) use ($parameterMeasurements, $parameter) {
                                $hourMeasurement = $parameterMeasurements->filter(function ($measurement) use ($hour) {
                                    if (empty($measurement->measured_at)) {
                                        return false;
                                    }

                                    return Carbon::parse($measurement->measured_at)->format('H') === $hour;
                                })->sortByDesc('measured_at')->first();

                                if (!$hourMeasurement) {
                                    return null;
                                }

                                $evaluation = $this->evaluateStandard($parameter->standard_text, $hourMeasurement->measured_value);

                                return [
                                    'measured_at' => $hourMeasurement->measured_at ? Carbon::parse($hourMeasurement->measured_at)->format('H:i') : '-',
                                    'measured_value' => $hourMeasurement->measured_value ?? $hourMeasurement->measured_text,
                                    'status_text' => $evaluation['status_text'],
                                    'is_abnormal' => $evaluation['is_abnormal'],
                                    'verification_label' => $hourMeasurement->batch_status === 'approved' ? 'Terverifikasi' : 'Belum diverifikasi',
                                    'verification_badge' => $hourMeasurement->batch_status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800',
                                    'batch_status' => $hourMeasurement->batch_status,
                                ];
                            })->values();

                            return [
                                'parameter_name' => $parameter->parameter_name,
                                'unit' => $parameter->unit,
                                'standard_text' => $parameter->standard_text,
                                'sampling_frequency' => $parameter->sampling_frequency,
                                'is_calculated' => (bool) $parameter->is_calculated,
                                'cells' => $cells,
                            ];
                        })->values(),
                    ];
                })->values();

                $level3Matrix->push([
                    'date' => $reportDate,
                    'hours' => $level3Hours,
                    'categories' => $categories,
                ]);
            }
        }

        return view('labsystem::report.quality-daily', compact(
            'startDate',
            'endDate',
            'scorecard',
            'samplingBatches',
            'verifiedBatches',
            'pendingBatches',
            'trendData',
            'shiftComparison',
            'rawTableData',
            'level3Matrix',
            'measurements'
        ));
    }
}
