<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    /**
     * List all job listings for moderation, newest first.
     * Supports optional filtering by moderation_status and a free-text search.
     */
    public function index(Request $request)
    {
        $query = JobListing::query()->with('employer');

        if ($request->moderation_status) {
            $query->where('moderation_status', $request->moderation_status);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('company', 'like', "%$search%");
            });
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function show(JobListing $jobListing)
    {
        return response()->json($jobListing->load(['employer', 'applications', 'comments.user']));
    }

    public function approve(Request $request, JobListing $jobListing)
    {
        Gate::authorize('moderate', $jobListing);

        $jobListing->update([
            'moderation_status' => 'approved',
            'rejection_reason' => null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Job listing approved successfully',
            'data' => $jobListing->fresh('employer'),
        ]);
    }

    public function reject(Request $request, JobListing $jobListing)
    {
        Gate::authorize('moderate', $jobListing);

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $jobListing->update([
            'moderation_status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Job listing rejected',
            'data' => $jobListing->fresh('employer'),
        ]);
    }

    /**
     * Admin can reset a job back to pending if it needs another look.
     */
    public function resetReview(Request $request, JobListing $jobListing)
    {
        Gate::authorize('moderate', $jobListing);

        $jobListing->update([
            'moderation_status' => 'pending',
            'rejection_reason' => null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Job listing moved back to pending review',
            'data' => $jobListing->fresh('employer'),
        ]);
    }

    public function destroy(Request $request, JobListing $jobListing)
    {
        Gate::authorize('moderate', $jobListing);

        $jobListing->delete();

        return response()->json(['message' => 'Job listing deleted successfully']);
    }
}
