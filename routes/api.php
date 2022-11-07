<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TodoController;
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
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('user-profile', [AuthController::class, 'userProfile']);

    Route::apiResource('todos', TodoController::class);
    Route::post('todos/changeStatus/{todo}', [TodoController::class, 'updateStatus'])->name('todos.updateStatus');

    Route::post('/logout', [AuthController::class, 'logout']);
});
