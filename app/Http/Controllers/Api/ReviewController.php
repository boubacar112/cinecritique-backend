<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\TMDbService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    private $tmdb;

    public function __construct(TMDbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    /**
     * Créer une nouvelle critique
     * POST /api/reviews
     */
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'movie_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        // Vérifier si l'utilisateur a déjà critiqué ce film
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('movie_id', $request->movie_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'Vous avez déjà critiqué ce film. Modifiez votre critique existante.',
            ], 422);
        }

        // Récupérer les infos du film depuis TMDb
        $movie = $this->tmdb->getMovieDetails($request->movie_id);

        if (isset($movie['success']) && $movie['success'] === false) {
            return response()->json([
                'message' => 'Film non trouvé',
            ], 404);
        }

        // Créer la critique
        $review = Review::create([
            'user_id' => $request->user()->id,
            'movie_id' => $request->movie_id,
            'movie_title' => $movie['title'],
            'movie_poster' => $movie['poster_path'] ?? null,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Charger la relation user
        $review->load('user');

        return response()->json([
            'message' => 'Critique créée avec succès',
            'review' => $review,
        ], 201);
    }

    /**
     * Récupérer toutes les critiques d'un film
     * GET /api/movies/{movie_id}/reviews
     */
    public function getMovieReviews($movie_id)
    {
        $reviews = Review::where('movie_id', $movie_id)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculer la moyenne des notes
        $averageRating = $reviews->avg('rating');

        return response()->json([
            'reviews' => $reviews,
            'total_reviews' => $reviews->count(),
            'average_rating' => $averageRating ? round($averageRating, 1) : 0,
        ]);
    }

    /**
     * Modifier sa propre critique
     * PUT /api/reviews/{id}
     */
    public function update(Request $request, $id)
    {
        // Validation
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        // Récupérer la critique
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'message' => 'Critique non trouvée',
            ], 404);
        }

        // Vérifier que c'est bien la critique de l'utilisateur connecté
        if ($review->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Vous ne pouvez modifier que vos propres critiques',
            ], 403);
        }

        // Mettre à jour
        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $review->load('user');

        return response()->json([
            'message' => 'Critique modifiée avec succès',
            'review' => $review,
        ]);
    }

    /**
     * Supprimer sa propre critique
     * DELETE /api/reviews/{id}
     */
    public function destroy(Request $request, $id)
    {
        // Récupérer la critique
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'message' => 'Critique non trouvée',
            ], 404);
        }

        // Vérifier que c'est bien la critique de l'utilisateur connecté
        if ($review->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Vous ne pouvez supprimer que vos propres critiques',
            ], 403);
        }

        // Supprimer
        $review->delete();

        return response()->json([
            'message' => 'Critique supprimée avec succès',
        ]);
    }

    /**
     * Récupérer toutes les critiques de l'utilisateur connecté
     * GET /api/my-reviews
     */
    public function myReviews(Request $request)
    {
        $reviews = Review::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'reviews' => $reviews,
            'total' => $reviews->count(),
        ]);
    }

    /**
     * Récupérer les critiques d'un utilisateur spécifique
     * GET /api/users/{user_id}/reviews
     */
    public function getUserReviews($user_id)
    {
        $reviews = Review::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'reviews' => $reviews,
            'total' => $reviews->count(),
        ]);
    }
}