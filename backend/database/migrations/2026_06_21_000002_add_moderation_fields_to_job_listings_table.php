<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * We intentionally do NOT touch the existing `status` column
     * (draft/published/archived) which the employer-facing flow relies on.
     * Admin moderation is layered on top with its own column so existing
     * employer behaviour keeps working unchanged.
     */
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            if (!Schema::hasColumn('job_listings', 'moderation_status')) {
                $table->enum('moderation_status', ['pending', 'approved', 'rejected'])
                    ->default('pending')
                    ->after('status');
            }

            if (!Schema::hasColumn('job_listings', 'rejection_reason')) {
                $table->string('rejection_reason')->nullable()->after('moderation_status');
            }

            if (!Schema::hasColumn('job_listings', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('rejection_reason');
            }

            if (!Schema::hasColumn('job_listings', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
        });

        Schema::table('job_listings', function (Blueprint $table) {
            $table->index('moderation_status');
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropIndex(['moderation_status']);

            if (Schema::hasColumn('job_listings', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }

            if (Schema::hasColumn('job_listings', 'reviewed_by')) {
                $table->dropConstrainedForeignId('reviewed_by');
            }

            if (Schema::hasColumn('job_listings', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }

            if (Schema::hasColumn('job_listings', 'moderation_status')) {
                $table->dropColumn('moderation_status');
            }
        });
    }
};
