<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Comment;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * High-level platform stats for the admin dashboard cards/widgets.
     */
    public function stats(Request $request)
    {
        $jobsByModeration = JobListing::query()
            ->selectRaw('moderation_status, count(*) as total')
            ->groupBy('moderation_status')
            ->pluck('total', 'moderation_status');

        $usersByRole = User::query()
            ->selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        return response()->json([
            'users' => [
                'total' => User::count(),
                'active' => User::where('status', 'active')->count(),
                'banned' => User::where('status', 'banned')->count(),
                'candidates' => $usersByRole->get('candidate', 0),
                'employers' => $usersByRole->get('employer', 0),
                'admins' => $usersByRole->get('admin', 0),
            ],
            'jobs' => [
                'total' => JobListing::count(),
                'pending' => $jobsByModeration->get('pending', 0),
                'approved' => $jobsByModeration->get('approved', 0),
                'rejected' => $jobsByModeration->get('rejected', 0),
            ],
            'applications' => [
                'total' => Application::count(),
                'pending' => Application::where('status', 'pending')->count(),
                'approved' => Application::where('status', 'approved')->count(),
                'rejected' => Application::where('status', 'rejected')->count(),
            ],
            'comments' => [
                'total' => Comment::count(),
                'visible' => Comment::where('status', 'visible')->count(),
                'hidden' => Comment::where('status', 'hidden')->count(),
            ],
            'recent_jobs' => JobListing::with('employer')->latest()->take(5)->get(),
            'recent_users' => User::latest()->take(5)->get(),
        ]);
    }
}
