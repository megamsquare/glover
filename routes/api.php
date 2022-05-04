<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CheckerController;
use App\Http\Controllers\API\UserController;

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

// Auth routes
// Register a new user route
Route::post('/register', [AuthController::class, 'register']);
// Login a user route
Route::post('/login', [AuthController::class, 'login']);

// Group routes
Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('user/update/{id}', [UserController::class, 'update']);
    Route::get('user/delete/{id}', [UserController::class, 'delete']);
    Route::get('pending_request', [CheckerController::class, 'get_pending_request']);
    Route::get('approve/{user_id}', [CheckerController::class, 'accept_request']);
    Route::get('decline/{user_id}', [CheckerController::class, 'decline_request']);
});
