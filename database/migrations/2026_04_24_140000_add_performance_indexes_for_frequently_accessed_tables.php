<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tickets')) {
            $this->addIndexIfMissing('tickets', 'tickets_user_status_created_idx', ['user_id', 'status', 'created_at']);
            $this->addIndexIfMissing('tickets', 'tickets_status_created_idx', ['status', 'created_at']);
        }

        if (Schema::hasTable('announcements')) {
            $this->addIndexIfMissing('announcements', 'ann_active_created_idx', ['is_active', 'created_at']);
        }

        if (Schema::hasTable('hr_external_data_requests')) {
            $this->addIndexIfMissing('hr_external_data_requests', 'hr_req_status_id_idx', ['status_proses', 'id']);
        }

        if (Schema::hasTable('uspk_submissions')) {
            $this->addIndexIfMissing('uspk_submissions', 'uspk_sub_status_created_idx', ['status', 'created_at']);
            $this->addIndexIfMissing('uspk_submissions', 'uspk_sub_submitter_created_idx', ['submitted_by', 'created_at']);
        }

        if (Schema::hasTable('pr_approvals')) {
            $this->addIndexIfMissing('pr_approvals', 'pra_pr_level_idx', ['purchase_request_id', 'level']);
        }

        if (Schema::hasTable('approver_configs')) {
            $this->addIndexIfMissing('approver_configs', 'apcfg_dept_level_idx', ['department_id', 'level']);
        }

        if (Schema::hasTable('global_approver_configs')) {
            $this->addIndexIfMissing('global_approver_configs', 'gapcfg_site_level_idx', ['site_id', 'level']);
            $this->addIndexIfMissing('global_approver_configs', 'gapcfg_user_site_idx', ['user_id', 'site_id']);
        }

        if (Schema::hasTable('activity_logs')) {
            $this->addIndexIfMissing('activity_logs', 'actlog_user_created_idx', ['user_id', 'created_at']);
            $this->addIndexIfMissing('activity_logs', 'actlog_system_created_idx', ['system', 'created_at']);
        }
    }

    public function down(): void
    {
        $this->dropIndexIfExists('tickets', 'tickets_user_status_created_idx');
        $this->dropIndexIfExists('tickets', 'tickets_status_created_idx');

        $this->dropIndexIfExists('announcements', 'ann_active_created_idx');

        $this->dropIndexIfExists('hr_external_data_requests', 'hr_req_status_id_idx');

        $this->dropIndexIfExists('uspk_submissions', 'uspk_sub_status_created_idx');
        $this->dropIndexIfExists('uspk_submissions', 'uspk_sub_submitter_created_idx');

        $this->dropIndexIfExists('pr_approvals', 'pra_pr_level_idx');

        $this->dropIndexIfExists('approver_configs', 'apcfg_dept_level_idx');

        $this->dropIndexIfExists('global_approver_configs', 'gapcfg_site_level_idx');
        $this->dropIndexIfExists('global_approver_configs', 'gapcfg_user_site_idx');

        $this->dropIndexIfExists('activity_logs', 'actlog_user_created_idx');
        $this->dropIndexIfExists('activity_logs', 'actlog_system_created_idx');
    }

    private function addIndexIfMissing(string $table, string $indexName, array $columns): void
    {
        if (!Schema::hasTable($table) || !$this->hasColumns($table, $columns) || $this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($columns, $indexName): void {
            $tableBlueprint->index($columns, $indexName);
        });
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (!Schema::hasTable($table) || !$this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($indexName): void {
            $tableBlueprint->dropIndex($indexName);
        });
    }

    private function hasColumns(string $table, array $columns): bool
    {
        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $dbName = DB::getDatabaseName();

        $rows = DB::select(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$dbName, $table, $indexName]
        );

        return !empty($rows);
    }
};
