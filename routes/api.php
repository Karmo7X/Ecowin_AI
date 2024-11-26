<?php

use App\Http\Controllers\api\BlogController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\ContactController;
use App\Http\Controllers\api\QuestionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// api routes for categories
Route::get('/categories', [CategoryController::class, 'index']);

// api routes for blogs

Route::get('/blogs',[BlogController::class,'index']);
Route::get('/blogs/{id}',[BlogController::class,'show']);

// api routes for contact
Route::post('contact',[ContactController::class,'store']);



// api routes for question (F.A.Q)

Route::get('/questions',[QuestionController::class,'index']);
Route::post('/search_questions',[QuestionController::class,'search']);
