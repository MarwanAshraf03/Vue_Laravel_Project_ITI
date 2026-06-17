<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = JobListing::query();

        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->location) {
            $query->where('location', $request->location);
        }
        if ($request->industry) {
            $query->where('industry', $request->industry);
        }
        if ($request->experience) {
            $query->where('experience_level', $request->experience);
        }
        if ($request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%$searchTerm%")
                  ->orWhere('description', 'like', "%$searchTerm%");
            });
        }
        if ($request->posted === '24h') {
            $query->where('created_at', '>=', now()->subDay());
        }
        if ($request->posted === '7days') {
            $query->where('created_at', '>=', now()->subDays(7));
        }
        if ($request->min_salary) {
            $query->where('salary_max', '>=', $request->min_salary);
        }
        if ($request->max_salary) {
            $query->where('salary_min', '<=', $request->max_salary);
        }

        $jobs = $query->latest()->get();

        return response()->json($jobs);
    }

    public function show(JobListing $jobListing)
    {
        return response()->json($jobListing);
    }
}
