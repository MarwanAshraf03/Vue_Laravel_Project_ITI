<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    public function store(Request $request, JobListing $jobListing)
    {
        $user = $request->user();

        // Check for duplicate application
        $existingApplication = Application::where('job_listing_id', $jobListing->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingApplication) {
            return response()->json(['message' => 'You have already applied to this job'], 409);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'candidate_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'message' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Prepare application data
        $applicationData = [
            'job_listing_id' => $jobListing->id,
            'user_id' => $user->id,
            'candidate_name' => $validated['candidate_name'] ?? $user->name,
            'email' => $validated['email'] ?? $user->email,
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'] ?? null,
        ];

        // Handle resume upload
        if ($request->hasFile('resume')) {
            $applicationData['resume_path'] = $request->file('resume')->store('resumes', 'public');
        }

        // Create application
        $application = Application::create($applicationData);

        return response()->json(['message' => 'Application submitted successfully', 'application' => $application], 201);
    }
}
