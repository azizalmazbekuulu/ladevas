<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
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

// Public Routes
Route::post('/sendinvitationemail', [AdminController::class, 'sendInvitaionEmail']);
Route::get('/register', [AdminController::class, 'register']);
Route::post('/registeruser', [AdminController::class, 'store']);
Route::post('/confirmregistration/{user}', [AdminController::class, 'confirmRegistration']);
Route::post('/loginuser', [AdminController::class, 'loginUser'])->middleware('guest');

// Protected Routes
Route::middleware(['auth:api'])->group(function () {
    Route::match(['post', 'patch'], 'updateprofile/{id}', [ProfileController::class, 'update']);
});
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});