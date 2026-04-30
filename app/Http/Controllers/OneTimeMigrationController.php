<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OneTimeMigrationController extends Controller
{
    /**
     * Run the specific migration for normalizing the jobs table.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function runJobNormalization(Request $request)
    {
        // IMPORTANT: Change this token to a long, random, and secret string!
        $secretToken = 'tukangkebun123';

        if ($request->input('token') !== $secretToken) {
            return response('Unauthorized.', 403);
        }

        $migrationName = '2026_04_15_130000_normalize_jobs_to_site_level';
        
        // Check if this migration has already been run
        $hasRun = DB::table('migrations')->where('migration', $migrationName)->exists();

        if ($hasRun) {
            return response('Migration already run.', 200);
        }

        try {
            DB::transaction(function () {
                $jobs = DB::table('jobs')
                    ->select('id', 'site_id', 'department_id', 'code', 'name')
                    ->orderBy('id')
                    ->get();

                $jobGroups = $jobs->groupBy(function ($job) {
                    return $job->site_id . '|' . $job->code;
                });

                $duplicateMap = [];

                foreach ($jobGroups as $group) {
                    if ($group->count() < 2) {
                        continue;
                    }
                    $keeper = $group->firstWhere('department_id', null) ?? $group->first();
                    foreach ($group as $job) {
                        if ((int) $job->id !== (int) $keeper->id) {
                            $duplicateMap[(int) $job->id] = (int) $keeper->id;
                        }
                    }
                }

                foreach ($duplicateMap as $duplicateId => $keeperId) {
                    if (Schema::hasTable('budgets') && Schema::hasColumn('budgets', 'job_id')) {
                        $duplicateBudgets = DB::table('budgets')->where('job_id', $duplicateId)->orderBy('id')->get();
                        foreach ($duplicateBudgets as $budgetRow) {
                            $keeperBudget = DB::table('budgets')->where('department_id', $budgetRow->department_id)->where('year', $budgetRow->year)->where('job_id', $keeperId)->first();
                            if ($keeperBudget) {
                                $updates = [
                                    'amount' => (float) $keeperBudget->amount + (float) $budgetRow->amount,
                                    'pta_amount' => (float) $keeperBudget->pta_amount + (float) $budgetRow->pta_amount,
                                    'used_amount' => (float) $keeperBudget->used_amount + (float) $budgetRow->used_amount,
                                    'updated_at' => now(),
                                ];
                                if (empty($keeperBudget->category) && !empty($budgetRow->category)) $updates['category'] = $budgetRow->category;
                                DB::table('budgets')->where('id', $keeperBudget->id)->update($updates);
                                DB::table('budgets')->where('id', $budgetRow->id)->delete();
                            } else {
                                DB::table('budgets')->where('id', $budgetRow->id)->update(['job_id' => $keeperId]);
                            }
                        }
                    }

                    foreach (['budgets_backup_full', 'pr_items', 'stock_movements', 'stock_movements_backup_full', 'uspk_submissions'] as $table) {
                        if (Schema::hasTable($table) && Schema::hasColumn($table, 'job_id')) {
                            DB::table($table)->where('job_id', $duplicateId)->update(['job_id' => $keeperId]);
                        }
                    }

                    if (Schema::hasTable('uspk_budget_activities') && Schema::hasColumn('uspk_budget_activities', 'job_id')) {
                        $duplicateActivities = DB::table('uspk_budget_activities')->where('job_id', $duplicateId)->orderBy('id')->get();
                        foreach ($duplicateActivities as $activityRow) {
                            $keeperActivity = DB::table('uspk_budget_activities')->where('sub_department_id', $activityRow->sub_department_id)->where('year', $activityRow->year)->where('job_id', $keeperId)->first();
                            if ($keeperActivity) {
                                $updates = [
                                    'budget_amount' => (float) $keeperActivity->budget_amount + (float) $activityRow->budget_amount,
                                    'used_amount' => (float) $keeperActivity->used_amount + (float) $activityRow->used_amount,
                                    'is_active' => (bool) $keeperActivity->is_active || (bool) $activityRow->is_active,
                                    'updated_at' => now(),
                                ];
                                if (!empty($keeperActivity->description) && !empty($activityRow->description)) $updates['description'] = trim($keeperActivity->description . ' | ' . $activityRow->description);
                                elseif (empty($keeperActivity->description) && !empty($activityRow->description)) $updates['description'] = $activityRow->description;
                                if (empty($keeperActivity->block_id) && !empty($activityRow->block_id)) $updates['block_id'] = $activityRow->block_id;
                                elseif (!empty($keeperActivity->block_id) && !empty($activityRow->block_id) && (int) $keeperActivity->block_id !== (int) $activityRow->block_id) $updates['block_id'] = null;
                                DB::table('uspk_budget_activities')->where('id', $keeperActivity->id)->update($updates);
                                DB::table('uspk_budget_activities')->where('id', $activityRow->id)->delete();
                            } else {
                                DB::table('uspk_budget_activities')->where('id', $activityRow->id)->update(['job_id' => $keeperId]);
                            }
                        }
                    }
                }

                if (!empty($duplicateMap)) {
                    DB::table('jobs')->whereIn('id', array_keys($duplicateMap))->delete();
                }

                DB::table('jobs')->update(['department_id' => null]);
            });

            // Schema changes (DDL) must be outside the transaction block.
            // Avoid Doctrine dependency; inspect index directly from information_schema.
            $indexExists = DB::table('information_schema.statistics')
                ->where('table_schema', DB::getDatabaseName())
                ->where('table_name', 'jobs')
                ->where('index_name', 'jobs_site_code_unique')
                ->exists();

            if (!$indexExists) {
                Schema::table('jobs', function ($table) {
                    $table->unique(['site_id', 'code'], 'jobs_site_code_unique');
                });
            }

            // Manually record the migration in the migrations table
            $batch = DB::table('migrations')->max('batch') + 1;
            DB::table('migrations')->insert([
                'migration' => $migrationName,
                'batch' => $batch
            ]);

            return response('Migration executed successfully!', 200);

        } catch (\Exception $e) {
            return response('Migration failed: ' . $e->getMessage(), 500);
        }
    }
}
