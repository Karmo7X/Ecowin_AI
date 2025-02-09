<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\BlogController;
use App\Http\Controllers\api\ContactController;
use App\Http\Controllers\api\PasswordController;
use App\Http\Controllers\api\QuestionController;
use App\Http\Controllers\api\ResetPassController;
use \App\Http\Controllers\api\ProudctController;
use \App\Http\Controllers\api\CategoryController;
use Illuminate\Support\Facades\Route;

Route::group([], function () {
    // Public routes (No Authentication Required)
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('send-otp', [PasswordController::class, 'sendOtp']);
    Route::post('forget-password', [PasswordController::class, 'forgetPassword']);

    // Public API routes (Accessible by everyone)
    Route::get('/blogs', [BlogController::class, 'index']);
    Route::get('/blogs/{id}', [BlogController::class, 'show']);

    Route::post('contact', [ContactController::class, 'store']);
    // questions api
    Route::get('/questions', [QuestionController::class, 'index']);
    Route::get('/question_search', [QuestionController::class, 'question_search']);
});

// Protected Routes for Any Authenticated User (No Role Required)
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/get_profile', [AuthController::class, 'GetProfile']);
    Route::post('/reset-password', [ResetPassController::class, 'resetPassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Routes accessible by users only
Route::group(['middleware' => ['auth:api', 'role:user']], function () {
    // Users specific routes here...
    //category api
    Route::get('/categories', [CategoryController::class, 'index']);
    // products api
    Route::get('/products', [ProudctController::class, 'index']);

});

// Agent routes (For Agents Only)
Route::group(['middleware' => ['auth:api', 'role:agent']], function () {
    // Agent specific routes here...
});

// Admin routes (For Admins Only)
Route::group(['middleware' => ['auth:api', 'role:admin']], function () {
    // Admins specific routes here...
});

