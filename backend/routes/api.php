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
