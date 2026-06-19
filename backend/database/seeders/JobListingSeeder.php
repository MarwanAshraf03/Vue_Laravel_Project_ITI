<?php

namespace Database\Seeders;

use App\Models\Employer;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobListingSeeder extends Seeder
{
    use WithoutModelEvents;

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
            [
                'description' => 'Seed employer profile',
                'phone' => '01000000000',
            ]
        );

        $jobs = [
            [
                'title' => 'Frontend Developer',
                'description' => 'We are looking for a talented frontend developer to join our team and work on modern web applications using Vue.js.',
                'category' => 'it',
                'industry' => 'software',
                'location' => 'cairo',
                'company' => 'Tech Solutions Inc.',
                'status' => 'published',
                'deadline' => now()->addDays(30),
                'salary_min' => 8000,
                'salary_max' => 15000,
                'experience_level' => 'junior'
            ],
            [
                'title' => 'Senior Backend Engineer',
                'description' => 'Join our backend team to design and implement scalable APIs using Laravel and PostgreSQL.',
                'category' => 'it',
                'industry' => 'software',
                'location' => 'giza',
                'company' => 'Digital Innovations',
                'status' => 'published',
                'deadline' => now()->addDays(35),
                'salary_min' => 15000,
                'salary_max' => 25000,
                'experience_level' => 'senior'
            ],
            [
                'title' => 'UI/UX Designer',
                'description' => 'Design beautiful and intuitive user interfaces for our products.',
                'category' => 'design',
                'industry' => 'creative',
                'location' => 'alexandria',
                'company' => 'Creative Minds',
                'status' => 'published',
                'deadline' => now()->addDays(40),
                'salary_min' => 6000,
                'salary_max' => 12000,
                'experience_level' => 'mid'
            ],
            [
                'title' => 'Marketing Specialist',
                'description' => 'Help us grow our brand and reach more customers.',
                'category' => 'marketing',
                'industry' => 'retail',
                'location' => 'cairo',
                'company' => 'Retail Hub',
                'status' => 'published',
                'deadline' => now()->addDays(28),
                'salary_min' => 5000,
                'salary_max' => 10000,
                'experience_level' => 'junior'
            ],
            [
                'title' => 'Full Stack Developer',
                'description' => 'Build end-to-end web applications using Vue.js and Laravel.',
                'category' => 'it',
                'industry' => 'fintech',
                'location' => 'cairo',
                'company' => 'FinTech Solutions',
                'status' => 'published',
                'deadline' => now()->addDays(45),
                'salary_min' => 12000,
                'salary_max' => 20000,
                'experience_level' => 'mid'
            ],
            [
                'title' => 'Data Analyst',
                'description' => 'Analyze data to provide insights for business decisions.',
                'category' => 'data',
                'industry' => 'finance',
                'location' => 'giza',
                'company' => 'Finance Corp',
                'status' => 'published',
                'deadline' => now()->addDays(32),
                'salary_min' => 7000,
                'salary_max' => 13000,
                'experience_level' => 'junior'
            ],
            [
                'title' => 'DevOps Engineer',
                'description' => 'Manage and optimize our cloud infrastructure.',
                'category' => 'it',
                'industry' => 'software',
                'location' => 'cairo',
                'company' => 'Cloud Services',
                'status' => 'published',
                'deadline' => now()->addDays(50),
                'salary_min' => 14000,
                'salary_max' => 22000,
                'experience_level' => 'senior'
            ],
            [
                'title' => 'Product Manager',
                'description' => 'Lead product development from concept to launch.',
                'category' => 'management',
                'industry' => 'tech',
                'location' => 'alexandria',
                'company' => 'Startup Hub',
                'status' => 'published',
                'deadline' => now()->addDays(60),
                'salary_min' => 10000,
                'salary_max' => 18000,
                'experience_level' => 'mid'
            ]
        ];

        foreach ($jobs as $job) {
            JobListing::updateOrCreate(
                ['title' => $job['title'], 'employer_id' => $employerUser->id],
                array_merge($job, ['employer_id' => $employerUser->id])
            );
        }
    }
}
