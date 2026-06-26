<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'category', 'industry', 'location', 'company', 'status', 'employer_id', 'deadline', 'salary_min', 'salary_max', 'experience_level', 'work_type', 'technologies', 'moderation_status', 'reviewed_by', 'reviewed_at', 'rejection_reason', 'requirements', 'benefits', 'company_logo'])]
class JobListing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'job_listings';

    protected $casts = [
        'deadline' => 'date',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function isPubliclyVisible(): bool
    {
        return $this->status === 'published' && $this->moderation_status === 'approved';
    }
}
