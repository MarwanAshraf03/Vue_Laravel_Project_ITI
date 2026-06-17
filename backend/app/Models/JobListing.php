<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'category', 'industry', 'location', 'company', 'salary_min', 'salary_max', 'experience_level'])]
class JobListing extends Model
{
    use HasFactory;

    protected $table = 'job_listings';

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
