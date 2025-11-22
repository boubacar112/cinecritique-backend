<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TMDbService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    private $tmdb;

    public function __construct(TMDbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    /**
     * Rechercher des films
     * GET /api/movies/search?query=inception
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1',
            'page' => 'integer|min:1',
        ]);

        $results = $this->tmdb->searchMovies(
            $request->query('query'),
            $request->query('page', 1)
        );

        return response()->json($results);
    }

    /**
     * Récupérer les détails d'un film
     * GET /api/movies/{id}
     */
    public function show($id)
    {
        $movie = $this->tmdb->getMovieDetails($id);

        if (isset($movie['success']) && $movie['success'] === false) {
            return response()->json([
                'message' => 'Film non trouvé'
            ], 404);
        }

        return response()->json($movie);
    }

    /**
     * Récupérer les films populaires
     * GET /api/movies/popular
     */
    public function popular(Request $request)
    {
        $results = $this->tmdb->getPopularMovies(
            $request->query('page', 1)
        );

        return response()->json($results);
    }

    /**
     * Récupérer les films les mieux notés
     * GET /api/movies/top-rated
     */
    public function topRated(Request $request)
    {
        $results = $this->tmdb->getTopRatedMovies(
            $request->query('page', 1)
        );

        return response()->json($results);
    }

    /**
     * Récupérer les genres
     * GET /api/genres
     */
    public function genres()
    {
        $genres = $this->tmdb->getGenres();

        return response()->json($genres);
    }

    /**
     * Découvrir des films avec filtres
     * GET /api/movies/discover?with_genres=28&year=2023
     */
    public function discover(Request $request)
    {
        $filters = $request->only([
            'with_genres',
            'year',
            'sort_by',
            'page',
            'primary_release_year',
            'vote_average.gte',
            'vote_average.lte'
        ]);

        $results = $this->tmdb->discoverMovies($filters);

        return response()->json($results);
    }
}