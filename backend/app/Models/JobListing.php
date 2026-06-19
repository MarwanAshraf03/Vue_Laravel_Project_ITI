<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'category', 'industry', 'location', 'employer_id', 'deadline', 'salary_min', 'salary_max', 'experience_level'])]
class JobListing extends Model
{
    use HasFactory;

    protected $table = 'job_listings';

    protected $casts = [
        'deadline' => 'date',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }
}
