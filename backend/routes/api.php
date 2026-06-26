<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::post('candidate/signup', [ProfileController::class, 'candidateSignUp']);
Route::post('admin/signup', [ProfileController::class, 'adminSignUp']);
Route::post('employer/signup', [ProfileController::class, 'employerSignUp']);
Route::post('user/login', [ProfileController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [ProfileController::class, 'get_current_user']);
    Route::post('user/logout', [ProfileController::class, 'logout']);
    Route::put('candidate/profile', [ProfileController::class, 'updateCandidateProfile']);
    Route::delete('candidate/profile', [ProfileController::class, 'deleteCandidateProfile']);
});

Route::get('jobs', [JobController::class, 'index']);
Route::get('jobs/{jobListing}', [JobController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('applications', [ApplicationController::class, 'index']);
    Route::get('user/applications', [ApplicationController::class, 'userApplications']);
    Route::delete('applications/{application}', [ApplicationController::class, 'destroy']);
    Route::get('candidate/applications', [ApplicationController::class, 'candidateApplications']);
    Route::post('jobs/{jobListing}/apply', [ApplicationController::class, 'store']);
    Route::post('jobs/create', [JobController::class, 'new']);
    Route::patch('jobs/update/{jobListing}', [JobController::class, 'update']);
    Route::delete('jobs/{jobListing}', [JobController::class, 'destroy']);

    Route::get('employer/jobs', [JobController::class, 'employerIndex']);
    Route::get('employer/jobs-summary', [JobController::class, 'employerJobsSummary']);
    Route::get('employer/jobs/{jobListing}/applications', [JobController::class, 'employerApplications']);

    Route::get('employer/applications', [ApplicationController::class, 'employerApplications']);
    Route::patch('employer/applications/{application}/approve', [ApplicationController::class, 'approve']);
    Route::patch('employer/applications/{application}/reject', [ApplicationController::class, 'reject']);
    Route::post('employer/applications/{application}/pay', [ApplicationController::class, 'pay']);
});

// ---- Admin routes ----
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('dashboard/stats', [App\Http\Controllers\Admin\DashboardController::class, 'stats']);

    // Jobs moderation
    Route::get('jobs', [App\Http\Controllers\Admin\JobController::class, 'index']);
    Route::get('jobs/{jobListing}', [App\Http\Controllers\Admin\JobController::class, 'show']);
    Route::patch('jobs/{jobListing}/approve', [App\Http\Controllers\Admin\JobController::class, 'approve']);
    Route::patch('jobs/{jobListing}/reject', [App\Http\Controllers\Admin\JobController::class, 'reject']);
    Route::patch('jobs/{jobListing}/reset-review', [App\Http\Controllers\Admin\JobController::class, 'resetReview']);
    Route::delete('jobs/{jobListing}', [App\Http\Controllers\Admin\JobController::class, 'destroy']);

    // Users management
    Route::get('users', [App\Http\Controllers\Admin\UserController::class, 'index']);
    Route::get('users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show']);
    Route::patch('users/{user}/ban', [App\Http\Controllers\Admin\UserController::class, 'ban']);
    Route::patch('users/{user}/unban', [App\Http\Controllers\Admin\UserController::class, 'unban']);
    Route::delete('users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy']);

    // Comments moderation
    Route::get('comments', [App\Http\Controllers\Admin\CommentController::class, 'index']);
    Route::patch('comments/{comment}/hide', [App\Http\Controllers\Admin\CommentController::class, 'hide']);
    Route::patch('comments/{comment}/unhide', [App\Http\Controllers\Admin\CommentController::class, 'unhide']);
    Route::delete('comments/{comment}', [App\Http\Controllers\Admin\CommentController::class, 'destroy']);
});