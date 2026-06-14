<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('candidates/signup', [ProfileController::class, 'candidateSignUp']);