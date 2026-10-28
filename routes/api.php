<?php

use Illuminate\Support\Facades\Route;

// API Routes here
Route::get('/v1/job-postings', [\App\Http\Controllers\JobPostingController::class, 'apiIndex']);
Route::post('/v1/job-applications', [\App\Http\Controllers\JobApplicationController::class, 'apiStore']);
