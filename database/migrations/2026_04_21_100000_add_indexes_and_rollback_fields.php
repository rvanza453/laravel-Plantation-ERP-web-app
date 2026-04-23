<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasIndex = function (string $table, string $indexName): bool {
            return !empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]));
        };

        $hasColumns = function (string $table, array $columns): bool {
            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    return false;
                }
            }

            return true;
        };

        Schema::table('uspk_approvals', function (Blueprint $table) {
            if (!Schema::hasColumn('uspk_approvals', 'previous_status')) {
                $table->string('previous_status')->nullable()->after('status')->comment('Status sebelum rollback');
            }

            if (!Schema::hasColumn('uspk_approvals', 'rollback_by_user_id')) {
                $table->foreignId('rollback_by_user_id')->nullable()->after('previous_status')->constrained('users')->nullOnDelete()->comment('User yang melakukan rollback');
            }

            if (!Schema::hasColumn('uspk_approvals', 'rollback_at')) {
                $table->timestamp('rollback_at')->nullable()->after('rollback_by_user_id');
            }
        });

        Schema::table('uspk_approvals', function (Blueprint $table) use ($hasIndex, $hasColumns) {
            if ($hasColumns('uspk_approvals', ['uspk_submission_id', 'level', 'status']) && !$hasIndex('uspk_approvals', 'uspk_approvals_uspk_submission_id_level_status_index') && !$hasIndex('uspk_approvals', 'uspk_approvals_uspk_submission_id_level_index')) {
                $table->index(['uspk_submission_id', 'level', 'status']);
            }

            if ($hasColumns('uspk_approvals', ['user_id', 'status', 'level']) && !$hasIndex('uspk_approvals', 'uspk_approvals_user_id_status_level_index')) {
                $table->index(['user_id', 'status', 'level']);
            }

            if ($hasColumns('uspk_approvals', ['approved_at']) && !$hasIndex('uspk_approvals', 'uspk_approvals_approved_at_index')) {
                $table->index(['approved_at']);
            }
        });

        Schema::table('uspk_submissions', function (Blueprint $table) use ($hasIndex, $hasColumns) {
            if ($hasColumns('uspk_submissions', ['status']) && !$hasIndex('uspk_submissions', 'uspk_submissions_status_index')) {
                $table->index(['status']);
            }

            if ($hasColumns('uspk_submissions', ['submitted_by']) && !$hasIndex('uspk_submissions', 'uspk_submissions_submitted_by_index')) {
                $table->index(['submitted_by']);
            }

            if ($hasColumns('uspk_submissions', ['created_at']) && !$hasIndex('uspk_submissions', 'uspk_submissions_created_at_index')) {
                $table->index(['created_at']);
            }

            if ($hasColumns('uspk_submissions', ['qc_status']) && !$hasIndex('uspk_submissions', 'uspk_submissions_qc_status_index')) {
                $table->index(['qc_status']);
            }

            if ($hasColumns('uspk_submissions', ['work_reported_completed_at']) && !$hasIndex('uspk_submissions', 'uspk_submissions_work_reported_completed_at_index')) {
                $table->index(['work_reported_completed_at']);
            }
        });

        if (Schema::hasTable('uspk_qc_verification_logs')) {
            Schema::table('uspk_qc_verification_logs', function (Blueprint $table) use ($hasIndex, $hasColumns) {
                if ($hasColumns('uspk_qc_verification_logs', ['uspk_submission_id', 'created_at']) && !$hasIndex('uspk_qc_verification_logs', 'uspk_qc_verification_logs_uspk_submission_id_created_at_index')) {
                    $table->index(['uspk_submission_id', 'created_at']);
                }

                if ($hasColumns('uspk_qc_verification_logs', ['action']) && !$hasIndex('uspk_qc_verification_logs', 'uspk_qc_verification_logs_action_index')) {
                    $table->index(['action']);
                }
            });
        }

        if (Schema::hasTable('uspk_block_progresses')) {
            Schema::table('uspk_block_progresses', function (Blueprint $table) use ($hasIndex, $hasColumns) {
                if ($hasColumns('uspk_block_progresses', ['uspk_submission_id', 'block_id']) && !$hasIndex('uspk_block_progresses', 'uspk_block_progresses_uspk_submission_id_block_id_index')) {
                    $table->index(['uspk_submission_id', 'block_id']);
                }

                if ($hasColumns('uspk_block_progresses', ['status']) && !$hasIndex('uspk_block_progresses', 'uspk_block_progresses_status_index')) {
                    $table->index(['status']);
                }

                if ($hasColumns('uspk_block_progresses', ['deadline_at']) && !$hasIndex('uspk_block_progresses', 'uspk_block_progresses_deadline_at_index')) {
                    $table->index(['deadline_at']);
                }
            });
        }

        if (Schema::hasTable('uspk_tenders')) {
            Schema::table('uspk_tenders', function (Blueprint $table) use ($hasIndex, $hasColumns) {
                if ($hasColumns('uspk_tenders', ['uspk_submission_id']) && !$hasIndex('uspk_tenders', 'uspk_tenders_uspk_submission_id_index')) {
                    $table->index(['uspk_submission_id']);
                }

                if ($hasColumns('uspk_tenders', ['contractor_id']) && !$hasIndex('uspk_tenders', 'uspk_tenders_contractor_id_index')) {
                    $table->index(['contractor_id']);
                }
            });
        }

        if (Schema::hasTable('uspk_qc_verifications')) {
            Schema::table('uspk_qc_verifications', function (Blueprint $table) use ($hasIndex, $hasColumns) {
                if ($hasColumns('uspk_qc_verifications', ['uspk_submission_id', 'user_id']) && !$hasIndex('uspk_qc_verifications', 'uspk_qc_verifications_uspk_submission_id_user_id_index')) {
                    $table->index(['uspk_submission_id', 'user_id']);
                }

                if ($hasColumns('uspk_qc_verifications', ['status']) && !$hasIndex('uspk_qc_verifications', 'uspk_qc_verifications_status_index')) {
                    $table->index(['status']);
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('uspk_approvals', function (Blueprint $table) {
            if (Schema::hasColumn('uspk_approvals', 'previous_status')) {
                $table->dropColumn('previous_status');
            }

            if (Schema::hasColumn('uspk_approvals', 'rollback_by_user_id')) {
                $table->dropConstrainedForeignId('rollback_by_user_id');
            }

            if (Schema::hasColumn('uspk_approvals', 'rollback_at')) {
                $table->dropColumn('rollback_at');
            }
        });
    }
};
