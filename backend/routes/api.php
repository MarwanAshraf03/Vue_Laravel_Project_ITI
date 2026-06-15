<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('candidate/signup', [ProfileController::class, 'candidateSignUp']);
Route::post('admin/signup', [ProfileController::class, 'adminSignUp']);
Route::post('employer/signup', [ProfileController::class, 'employerSignUp']);
Route::post('user/login', [ProfileController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [ProfileController::class, 'get_current_user']);
});
