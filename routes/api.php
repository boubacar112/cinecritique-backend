<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ProfileController;

/*
|--------------------------------------------------------------------------
| Routes API d'authentification (publiques)
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Routes API Films (publiques - pas besoin d'authentification)
|--------------------------------------------------------------------------
*/

Route::get('/movies/search', [MovieController::class, 'search']);
Route::get('/movies/popular', [MovieController::class, 'popular']);
Route::get('/movies/top-rated', [MovieController::class, 'topRated']);
Route::get('/movies/discover', [MovieController::class, 'discover']);
Route::get('/movies/{id}', [MovieController::class, 'show']);
Route::get('/genres', [MovieController::class, 'genres']);

/*
|--------------------------------------------------------------------------
| Routes API Reviews (publiques - consultation)
|--------------------------------------------------------------------------
*/

Route::get('/movies/{movie_id}/reviews', [ReviewController::class, 'getMovieReviews']);
Route::get('/users/{user_id}/reviews', [ReviewController::class, 'getUserReviews']);

/*
|--------------------------------------------------------------------------
| Routes API Profils (publiques - consultation)
|--------------------------------------------------------------------------
*/

Route::get('/profiles/{username}', [ProfileController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Routes API protégées (nécessitent un token)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    
    // Authentification
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Profil utilisateur
    Route::put('/profile', [ProfileController::class, 'update']);
    
    // Reviews (critiques) - Actions protégées
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
    Route::get('/my-reviews', [ReviewController::class, 'myReviews']);
    
});