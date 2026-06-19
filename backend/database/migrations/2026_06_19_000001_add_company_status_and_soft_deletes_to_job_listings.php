<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            if (!Schema::hasColumn('job_listings', 'company')) {
                $table->string('company')->nullable()->after('location');
            }

            if (!Schema::hasColumn('job_listings', 'status')) {
                $table->enum('status', ['draft', 'published', 'archived'])->default('published')->after('company');
            }

            if (!Schema::hasColumn('job_listings', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            if (Schema::hasColumn('job_listings', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            if (Schema::hasColumn('job_listings', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('job_listings', 'company')) {
                $table->dropColumn('company');
            }
        });
    }
};