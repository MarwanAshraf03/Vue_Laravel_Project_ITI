<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationPayment;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;



class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $applications = Application::with(['user', 'jobListing'])->get();
        return response()->json(['applications' => $applications], 200);
    }

    public function userApplications(Request $request)
    {
        $user = $request->user();
        $applications = Application::with(['jobListing'])->where('user_id', $user->id)->get();
        return response()->json(['applications' => $applications], 200);
    }

    public function store(Request $request, JobListing $jobListing)
    {
        $user = $request->user();

        if (!$jobListing->isPubliclyVisible()) {
            return response()->json(['message' => 'This job is not available for applications'], 404);
        }

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

    public function destroy(Request $request, Application $application)
    {
        $user = $request->user();

        if ($user->role !== 'admin' && ($user->role !== 'candidate' || $application->user_id !== $user->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $application->delete();
        return response()->json(['message' => 'Application deleted successfully'], 200);
    }

    public function candidateApplications(Request $request)
    {
        abort_unless($request->user()?->role === 'candidate', 403, 'Unauthorized action');

        return response()->json(
            Application::query()
                ->where('user_id', $request->user()->id)
                ->with(['jobListing'])
                ->latest()
                ->get()
        );
    }

    public function employerApplications(Request $request)
    {
        $this->ensureEmployer($request);

        return response()->json(
            Application::query()
                ->whereHas('jobListing', fn($query) => $query->where('employer_id', $request->user()->id))
                ->with(['jobListing', 'user', 'paymentTransaction', 'reviewer'])
                ->latest()
                ->get()
        );
    }

    public function review(Request $request, Application $application)
    {
        $jobListing = $application->jobListing;
        $this->ensureEmployer($request);
        $this->ensureJobOwnership($request, $jobListing);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
        ]);

        $application->update([
            'status' => $validated['status'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Application updated successfully',
            'application' => $application->fresh(['jobListing', 'user', 'paymentTransaction', 'reviewer']),
        ]);
    }

    public function approve(Request $request, Application $application)
    {
        Log::info("rejecting");
        $request->merge(['status' => 'approved']);
        return $this->review($request, $application);
    }

    public function reject(Request $request, Application $application)
    {
        $request->merge(['status' => 'rejected']);
        return $this->review($request, $application);
    }

    public function pay(Request $request, Application $application)
    {
        $jobListing = $application->jobListing;
        $this->ensureEmployer($request);
        $this->ensureJobOwnership($request, $jobListing);

        if ($application->status !== 'approved') {
            return response()->json(['message' => 'Approve the application before payment'], 422);
        }

        $validated = $request->validate([
            'provider' => ['required', Rule::in(['stripe', 'paypal'])],
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'sometimes|string|size:3',
            'reference' => 'nullable|string|max:255',
            'payload' => 'nullable|array',
        ]);

        $payment = ApplicationPayment::updateOrCreate(
            ['application_id' => $application->id],
            [
                'provider' => $validated['provider'],
                'amount' => $validated['amount'],
                'currency' => strtoupper($validated['currency'] ?? 'USD'),
                'status' => 'paid',
                'reference' => $validated['reference'] ?? null,
                'payload' => $validated['payload'] ?? [],
                'paid_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Payment recorded successfully',
            'payment' => $payment,
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
