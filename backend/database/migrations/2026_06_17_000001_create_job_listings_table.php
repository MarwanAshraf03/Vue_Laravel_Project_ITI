<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->string('industry');
            $table->string('location');
            $table->string('company')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('published');
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('salary_min');
            $table->unsignedInteger('salary_max');
            $table->string('experience_level');
            $table->dateTime('deadline');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['title', 'employer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
