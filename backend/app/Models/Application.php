<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['job_listing_id', 'user_id', 'candidate_name', 'email', 'phone', 'resume_path', 'message', 'status', 'reviewed_by', 'reviewed_at'])]
class Application extends Model
{
    use HasFactory;

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function paymentTransaction(): HasOne
    {
        return $this->hasOne(ApplicationPayment::class);
    }
}
