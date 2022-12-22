<?php

//use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\RegisterUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Auth__User
Route::post('/user/register', [RegisterUserController::class, 'register']);
Route::post('/user/login', [RegisterUserController::class, 'login']);
Route::get('/user/profile', [RegisterUserController::class, 'profile'])->middleware('APIToken');
Route::post('/user/logout', [RegisterUserController::class, 'logout'])->middleware('APIToken');
Route::post('/user/verified', [RegisterUserController::class, 'verification'])->middleware('APIToken');
Route::post('/user/update', [RegisterUserController::class, 'update'])->middleware('APIToken');

//Resest__Password
Route::post('/user/forget-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('/user/reset-password', [ForgotPasswordController::class, 'resetPassword']);

//Images
Route::post('/image/upload', [ImageController::class, 'upload'])->middleware('APIToken');
Route::post('/image/delete/{image}', [ImageController::class, 'delete'])->middleware('APIToken');
Route::post('/image/search', [ImageController::class, 'search'])->middleware('APIToken');
Route::post('/image/show', [ImageController::class, 'list']);
Route::post('/image/link', [ImageController::class, 'shareLink'])->middleware('APIToken');