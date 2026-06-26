<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'job_listing_id', 'content', 'status', 'moderated_by', 'moderated_at'])]
class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'moderated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }
}
