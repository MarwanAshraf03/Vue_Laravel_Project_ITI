<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Employer;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminDemoSeeder extends Seeder
{
    /**
     * Adds a couple of pending/rejected job listings and a sample comment
     * so the new admin moderation screens have something to show out of the box.
     * Safe to re-run: uses updateOrCreate / firstOrCreate throughout.
     */
    public function run(): void
    {
        $employerUser = User::firstOrCreate(
            ['email' => 'employer@example.com'],
            [
                'name' => 'Seed Employer',
                'password' => 'password',
                'role' => 'employer',
            ]
        );

        Employer::firstOrCreate(
            ['user_id' => $employerUser->id],
            ['description' => 'Seed employer profile', 'phone' => '01000000000']
        );

        $pendingJob = JobListing::updateOrCreate(
            ['title' => 'Junior QA Engineer', 'employer_id' => $employerUser->id],
            [
                'description' => 'Awaiting admin review — manual and automated testing for our web platform.',
                'category' => 'it',
                'industry' => 'software',
                'location' => 'cairo',
                'company' => 'Tech Solutions Inc.',
                'status' => 'published',
                'moderation_status' => 'pending',
                'deadline' => now()->addDays(30),
                'salary_min' => 6000,
                'salary_max' => 10000,
                'experience_level' => 'junior',
            ]
        );

        JobListing::updateOrCreate(
            ['title' => 'Crypto Trading Bot Operator', 'employer_id' => $employerUser->id],
            [
                'description' => 'Rejected sample listing to demonstrate the admin rejection workflow.',
                'category' => 'finance',
                'industry' => 'crypto',
                'location' => 'remote',
                'company' => 'Unverified Ventures',
                'status' => 'published',
                'moderation_status' => 'rejected',
                'rejection_reason' => 'Listing did not meet platform compliance guidelines.',
                'reviewed_at' => now(),
                'deadline' => now()->addDays(20),
                'salary_min' => 5000,
                'salary_max' => 9000,
                'experience_level' => 'mid',
            ]
        );

        $candidateUser = User::firstOrCreate(
            ['email' => 'candidate@example.com'],
            [
                'name' => 'Seed Candidate',
                'password' => 'password',
                'role' => 'candidate',
            ]
        );

        $candidateUser->profile()->firstOrCreate(
            ['user_id' => $candidateUser->id],
            ['job_title' => 'Aspiring Developer']
        );

        $firstApprovedJob = JobListing::where('moderation_status', 'approved')->first();

        if ($firstApprovedJob) {
            Comment::firstOrCreate(
                [
                    'user_id' => $candidateUser->id,
                    'job_listing_id' => $firstApprovedJob->id,
                    'content' => 'Is this role open to remote applicants?',
                ]
            );
        }

        unset($pendingJob);
    }
}
