<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\ProfileController;

Route::get('/profiles/{username}', [ProfileController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/movies/search', [MovieController::class, 'search']);
Route::get('/movies/popular', [MovieController::class, 'popular']);
Route::get('/movies/top-rated', [MovieController::class, 'topRated']);
Route::get('/movies/discover', [MovieController::class, 'discover']);
Route::get('/movies/{id}', [MovieController::class, 'show']);
Route::get('/genres', [MovieController::class, 'genres']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});
