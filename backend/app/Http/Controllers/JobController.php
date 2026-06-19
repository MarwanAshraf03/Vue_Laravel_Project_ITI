<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
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

    public function new(Request $request)
    {
        // 1. Validate the incoming job data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:job_listings,title',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'deadline' => 'required|date_format:d-m-Y',
            'salary_min' => 'required|integer|min:0',
            'salary_max' => 'required|integer|gte:salary_min', // Must be greater than or equal to salary_min
            'experience_level' => 'required|string|max:255',
        ]);

        // 2. Return validation errors if it fails
        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // 3. Inject the ID directly from the authenticated user token
        $data['employer_id'] = $request->user()->id;

        // 3. Create and save the new job listing
        $job = JobListing::create($data);

        // 4. Return successful JSON response for your Vue frontend
        return response()->json([
            'ok' => true,
            'message' => 'Job listing created successfully!',
            'data' => $job,
        ], 201);
    }

    public function update(Request $request, JobListing $jobListing)
    {
        $validated = $request->validate([
            // The 'unique' rule ignores the current record's ID so it doesn't fail on itself
            'title' => 'sometimes|string|max:255|unique:job_listings,title,' . $jobListing->id,
            'description' => 'sometimes|string',
            'category' => 'sometimes|string',
            'industry' => 'sometimes|string',
            'deadline' => 'sometimes|date_format:d-m-Y',
            'salary_min' => 'sometimes|numeric',
            'salary_max' => 'sometimes|numeric',
            'experience_level' => 'sometimes|string',
            'location' => 'sometimes|string',
        ]);

        $jobListing->update($validated);

        return response()->json([
            'message' => 'Job listing updated successfully',
            'data' => $jobListing
        ], 200);
    }

}
