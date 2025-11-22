<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TMDbService
{
    private $apiKey;
    private $baseUrl;
    private $imageBaseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.api_key');
        $this->baseUrl = config('services.tmdb.base_url');
        $this->imageBaseUrl = config('services.tmdb.image_base_url');
    }

    /**
     * Rechercher des films par titre
     */
    public function searchMovies($query, $page = 1)
    {
        $response = Http::get("{$this->baseUrl}/search/movie", [
            'api_key' => $this->apiKey,
            'query' => $query,
            'language' => 'fr-FR',
            'page' => $page,
        ]);

        return $response->json();
    }

    /**
     * Récupérer les détails d'un film
     */
    public function getMovieDetails($movieId)
    {
        $response = Http::get("{$this->baseUrl}/movie/{$movieId}", [
            'api_key' => $this->apiKey,
            'language' => 'fr-FR',
            'append_to_response' => 'credits,videos', // Récupère aussi le casting et les vidéos
        ]);

        return $response->json();
    }

    /**
     * Récupérer les films populaires
     */
    public function getPopularMovies($page = 1)
    {
        $response = Http::get("{$this->baseUrl}/movie/popular", [
            'api_key' => $this->apiKey,
            'language' => 'fr-FR',
            'page' => $page,
        ]);

        return $response->json();
    }

    /**
     * Récupérer les films les mieux notés
     */
    public function getTopRatedMovies($page = 1)
    {
        $response = Http::get("{$this->baseUrl}/movie/top_rated", [
            'api_key' => $this->apiKey,
            'language' => 'fr-FR',
            'page' => $page,
        ]);

        return $response->json();
    }

    /**
     * Récupérer les genres de films
     */
    public function getGenres()
    {
        $response = Http::get("{$this->baseUrl}/genre/movie/list", [
            'api_key' => $this->apiKey,
            'language' => 'fr-FR',
        ]);

        return $response->json();
    }

    /**
     * Découvrir des films avec des filtres
     */
    public function discoverMovies($filters = [])
    {
        $params = array_merge([
            'api_key' => $this->apiKey,
            'language' => 'fr-FR',
            'sort_by' => 'popularity.desc',
            'page' => 1,
        ], $filters);

        $response = Http::get("{$this->baseUrl}/discover/movie", $params);

        return $response->json();
    }

    /**
     * Construire l'URL complète d'une image
     */
    public function getImageUrl($path, $size = 'w500')
    {
        if (!$path) {
            return null;
        }
        return "{$this->imageBaseUrl}/{$size}{$path}";
    }
}