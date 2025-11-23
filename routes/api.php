<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\ReviewController;

Route::post('/register', [AuthController::class, 'register']); //Register a new user
Route::post('/login', [AuthController::class, 'login']);  //Login an existing user

Route::get('/movies/search', [MovieController::class, 'search']);   //Rechercher des films
Route::get('/movies/popular', [MovieController::class, 'popular']); //Récupérer les films
Route::get('/movies/top-rated', [MovieController::class, 'topRated']); //Récupérer les films les mieux notés
Route::get('/movies/discover', [MovieController::class, 'discover']);  //Découvrir des films
Route::get('/movies/{id}', [MovieController::class, 'show']);      //Récupérer les détails d'un film
Route::get('/genres', [MovieController::class, 'genres']);         //Récupérer les genres

// Reviews (critiques) - Actions publiques

Route::get('/movies/{movie_id}/reviews', [ReviewController::class, 'getMovieReviews']);
Route::get('/users/{user_id}/reviews', [ReviewController::class, 'getUserReviews']);

Route::middleware('auth:sanctum')->group(function () {
    // Authenticated user actions
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

  // Reviews (critiques) - Actions protégées
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
    Route::get('/my-reviews', [ReviewController::class, 'myReviews']);  
}); //Protected routes for authenticated users