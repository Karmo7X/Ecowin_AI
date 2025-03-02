<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\BlogController;
use App\Http\Controllers\api\ContactController;
use App\Http\Controllers\api\PasswordController;
use App\Http\Controllers\api\QuestionController;
use App\Http\Controllers\api\ResetPassController;
use \App\Http\Controllers\api\ProudctController;
use \App\Http\Controllers\api\CategoryController;
use \App\Http\Controllers\api\CartController;
use \App\Http\Controllers\api\OrderController;
use \App\Http\Controllers\api\LeaderboardController;
use Illuminate\Support\Facades\Route;

Route::group([], function () {
    // Public routes (No Authentication Required)
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('send-otp', [PasswordController::class, 'sendOtp']);
    Route::post('forget-password', [PasswordController::class, 'forgetPassword']);
    Route::get('/top-users', [LeaderboardController::class, 'topUsers']);



});

// Protected Routes for Any Authenticated User (No Role Required)
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/get_profile', [AuthController::class, 'GetProfile']);
    Route::post('/edit_profile', [AuthController::class, 'EditProfile']);
    Route::post('/reset-password', [ResetPassController::class, 'resetPassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Routes accessible by users only
Route::group(['middleware' => ['auth:api','changelanguage','role:user']], function () {
    // Users specific routes here...

    Route::get('/blogs', [BlogController::class, 'index']);
    Route::get('/blogs/{id}', [BlogController::class, 'show']);

    Route::post('contact', [ContactController::class, 'store']);
    // questions api
    Route::get('/questions', [QuestionController::class, 'index']);
    Route::get('/question_search', [QuestionController::class, 'question_search']);
    //category api
    Route::get('/categories', [CategoryController::class, 'index']);
    // products api
    Route::get('/products', [ProudctController::class, 'index']);
    // cart api
    Route::post('/add_to_cart', [CartController::class, 'store']);
    Route::patch('/cart/item/{cartItemId}', [CartController::class, 'updateCartItem']);
    //order
    Route::post('/confirm_order', [OrderController::class, 'confirmOrder']);

});

// Agent routes (For Agents Only)
Route::group(['middleware' => ['auth:api', 'role:agent']], function () {
    // Agent specific routes here...
});

// Admin routes (For Admins Only)
Route::group(['middleware' => ['auth:api', 'role:admin']], function () {
    // Admins specific routes here...
});

