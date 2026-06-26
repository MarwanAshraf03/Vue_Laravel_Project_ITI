<?php

namespace App\Policies;

use App\Models\JobListing;
use App\Models\User;

class JobListingPolicy
{
    /**
     * Admins can moderate (approve/reject/delete) any job listing.
     * Employers may only manage job listings they own.
     */
    public function moderate(User $user, JobListing $jobListing): bool
    {
        return $user->role === 'admin';
    }

    public function manage(User $user, JobListing $jobListing): bool
    {
        return $user->role === 'admin' || $jobListing->employer_id === $user->id;
    }
}
