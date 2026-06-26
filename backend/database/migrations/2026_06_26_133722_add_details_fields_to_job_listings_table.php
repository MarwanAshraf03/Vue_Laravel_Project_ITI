<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->enum('work_type', ['remote', 'onsite', 'hybrid'])->default('remote')->after('location');
            $table->text('technologies')->nullable()->after('work_type');
            $table->text('requirements')->nullable()->after('technologies');
            $table->text('benefits')->nullable()->after('requirements');
            $table->string('company_logo')->nullable()->after('benefits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropColumn(['work_type', 'technologies', 'requirements', 'benefits', 'company_logo']);
        });
    }
};
