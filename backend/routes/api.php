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
});
