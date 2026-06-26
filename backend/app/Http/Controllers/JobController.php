<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Support\Facades\Validator;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = JobListing::query()
            ->where('status', 'published')
            ->where('moderation_status', 'approved');

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

    public function show(Request $request, JobListing $jobListing)
    {
        $user = $request->user();
        $isOwner = $user && $user->id === $jobListing->employer_id;
        $isAdmin = $user && $user->role === 'admin';

        if (!$isOwner && !$isAdmin && !$jobListing->isPubliclyVisible()) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        return response()->json($jobListing->load(['employer.profile']));
    }

    public function employerIndex(Request $request)
    {
        $this->ensureEmployer($request);

        $jobs = JobListing::query()
            ->where('employer_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($jobs);
    }

    public function new(Request $request)
    {
        $this->ensureEmployer($request);

        // 1. Validate the incoming job data
        $validator = Validator::make($request->all(), [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('job_listings', 'title')->where(fn ($query) => $query->where('employer_id', $request->user()->id)),
            ],
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'deadline' => 'required|date_format:d-m-Y',
            'salary_min' => 'required|integer|min:0',
            'salary_max' => 'required|integer|gte:salary_min', // Must be greater than or equal to salary_min
            'experience_level' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(['draft', 'published', 'archived'])],
            'work_type' => ['required', Rule::in(['remote', 'onsite', 'hybrid'])],
            'technologies' => 'nullable|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // 2. Return validation errors if it fails
        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Handle company logo file upload
        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('company_logos', 'public');
        }

        // 3. Inject the owner and default publication state directly from the authenticated user token
        $data['employer_id'] = $request->user()->id;
        $data['company'] = $data['company'] ?? $request->user()->name;
        $data['status'] = $data['status'] ?? 'published';

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
        $this->ensureEmployer($request);
        $this->ensureJobOwnership($request, $jobListing);

        $validated = $request->validate([
            // The 'unique' rule ignores the current record's ID so it doesn't fail on itself
            'title' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('job_listings', 'title')
                    ->ignore($jobListing->id)
                    ->where(fn ($query) => $query->where('employer_id', $request->user()->id)),
            ],
            'description' => 'sometimes|string',
            'category' => 'sometimes|string',
            'industry' => 'sometimes|string',
            'deadline' => 'sometimes|date_format:d-m-Y',
            'salary_min' => 'sometimes|numeric',
            'salary_max' => 'sometimes|numeric',
            'experience_level' => 'sometimes|string',
            'location' => 'sometimes|string',
            'company' => 'sometimes|string|max:255',
            'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])],
            'work_type' => ['sometimes', Rule::in(['remote', 'onsite', 'hybrid'])],
            'technologies' => 'nullable|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle company logo file upload
        if ($request->hasFile('company_logo')) {
            if ($jobListing->company_logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($jobListing->company_logo);
            }
            $validated['company_logo'] = $request->file('company_logo')->store('company_logos', 'public');
        }

        $jobListing->update($validated);

        return response()->json([
            'message' => 'Job listing updated successfully',
            'data' => $jobListing
        ], 200);
    }

    public function destroy(Request $request, JobListing $jobListing)
    {
        $this->ensureEmployer($request);
        $this->ensureJobOwnership($request, $jobListing);

        $jobListing->delete();

        return response()->json([
            'message' => 'Job listing deleted successfully',
        ]);
    }

    public function employerApplications(Request $request, JobListing $jobListing)
    {
        $this->ensureEmployer($request);
        $this->ensureJobOwnership($request, $jobListing);

        return response()->json(
            $jobListing->applications()->with(['user', 'paymentTransaction', 'reviewer'])->latest()->get()
        );
    }

    public function employerJobsSummary(Request $request)
    {
        return response()->json([
            'jobs' => JobListing::query()->where('employer_id', $request->user()->id)->latest()->get(),
            'applications' => Application::query()
                ->whereHas('jobListing', fn ($query) => $query->where('employer_id', $request->user()->id))
                ->with(['jobListing', 'user', 'paymentTransaction', 'reviewer'])
                ->latest()
                ->get(),
        ]);
    }

    private function ensureEmployer(Request $request): void
    {
        abort_unless($request->user()?->role === 'employer', 403, 'Unauthorized action');
    }

    private function ensureJobOwnership(Request $request, JobListing $jobListing): void
    {
        abort_unless($jobListing->employer_id === $request->user()->id, 403, 'Unauthorized action');
    }

}
