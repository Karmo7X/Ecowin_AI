<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\BlogController;
use App\Http\Controllers\api\BrandController;
use App\Http\Controllers\api\ContactController;
use App\Http\Controllers\api\PasswordController;
use App\Http\Controllers\api\QuestionController;
use App\Http\Controllers\api\ResetPassController;
use \App\Http\Controllers\api\ProudctController;
use \App\Http\Controllers\api\CategoryController;
use \App\Http\Controllers\api\CartController;
use \App\Http\Controllers\api\OrderController;
use \App\Http\Controllers\api\LeaderboardController;
use \App\Http\Controllers\api\CopounController;
use App\Http\Controllers\api\TransactionController;
use App\Http\Controllers\api\WalletController;
use App\Http\Controllers\api\DonationController;
use Illuminate\Support\Facades\Route;

Route::group(['changelanguage'], function () {
    // Public routes (No Authentication Required)
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('send-otp', [PasswordController::class, 'sendOtp']);
    Route::post('/confirm_otp', [PasswordController::class, 'confirmOtp']);
    Route::post('forget-password', [PasswordController::class, 'forgetPassword']);
    Route::get('/top-users', [LeaderboardController::class, 'topUsers']);

    Route::get('/blogs', [BlogController::class, 'index']);
    Route::get('/blogs/{id}', [BlogController::class, 'show']);

    Route::post('/contact', [ContactController::class, 'store']);
    // questions api
    Route::get('/questions', [QuestionController::class, 'index']);
    Route::get('/question_search', [QuestionController::class, 'question_search']);

    // brands api
    Route::get('/brands', [BrandController::class, 'index']);

});

// Protected Routes for Any Authenticated User (No Role Required)
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/get_profile', [AuthController::class, 'GetProfile']);
    Route::post('/edit_profile', [AuthController::class, 'EditProfile']);
    Route::post('/update_profile_image', [AuthController::class, 'UpdateProfileImage']);
    Route::post('/reset-password', [ResetPassController::class, 'resetPassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Routes accessible by users only
Route::group(['middleware' => ['auth:api','changelanguage','role:user']], function () {
    // Users specific routes here...


    //category api
    Route::get('/categories', [CategoryController::class, 'index']);
    // products api
    Route::get('/products', [ProudctController::class, 'index']);
    // cart api
    Route::get('/cart/get', [CartController::class, 'index']);
    Route::post('/add_to_cart', [CartController::class, 'store']);
    Route::patch('/cart/update', [CartController::class, 'update']);
    Route::delete('/cart/delete', [CartController::class, 'delete']);
    Route::delete('/cart/item/{itemId}', [CartController::class, 'removeItem']);
    //order
    Route::post('/confirm_order', [OrderController::class, 'confirmOrder']);
    Route::get('/my_orders', [OrderController::class, 'myorders']);
    // coupons api
    Route::get('/coupons',[CopounController::class, 'index']);
    Route::post('/redeem_coupon',[CopounController::class, 'redeemCoupon']);
    Route::get('/my_coupons',[CopounController::class, 'myCoupons']);
    // wallet api
    Route::get('/wallet', [WalletController::class, 'getWallet']);
    //transaction api
    Route::get('/transactions', [TransactionController::class, 'index']);
    //charity api
    Route::post('/donate', [DonationController::class, 'store']);
});

// Agent routes (For Agents Only)
Route::group(['middleware' => ['auth:api', 'role:agent']], function () {
    // Agent specific routes here...
});

// Admin routes (For Admins Only)
Route::group(['middleware' => ['auth:api', 'role:admin']], function () {
    // Admins specific routes here...
});

