<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Genre;
use App\Models\WatchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $query = Movie::query();

        // Only show movies that have been uploaded (have cdn_path)
        $query->whereNotNull('cdn_path');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by genre
        if ($request->has('genre') && $request->genre) {
            $query->whereHas('genres', function($q) use ($request) {
                $q->where('slug', $request->genre);
            });
        }

        $movies = $query->with('genres')->latest()->paginate(20);
        $genres = Genre::all();
        $featured = Movie::where('is_featured', true)->whereNotNull('cdn_path')->with('genres')->take(5)->get();
        $trending = Movie::where('is_trending', true)->whereNotNull('cdn_path')->with('genres')->take(10)->get();

        return view('movies.index', compact('movies', 'genres', 'featured', 'trending'));
    }

    public function show($slug)
    {
        $movie = Movie::where('slug', $slug)->with('genres')->firstOrFail();
        
        // Check if movie is still uploading
        if (!$movie->cdn_path) {
            return view('movies.uploading', compact('movie'));
        }
        
        // Increment views
        $movie->increment('views');

        // Get similar movies (same genres, only uploaded ones)
        $similarMovies = Movie::where('id', '!=', $movie->id)
            ->whereNotNull('cdn_path')
            ->whereHas('genres', function($q) use ($movie) {
                $q->whereIn('genres.id', $movie->genres->pluck('id'));
            })
            ->with('genres')
            ->take(6)
            ->get();

        // Get watch progress if user is logged in
        $watchProgress = 0;
        $isFavorited = false;
        if (Auth::check()) {
            $watchHistory = WatchHistory::where('user_id', Auth::id())
                ->where('movie_id', $movie->id)
                ->first();
            if ($watchHistory) {
                $watchProgress = $watchHistory->progress;
            }
            
            $isFavorited = \App\Models\Favorite::where('user_id', Auth::id())
                ->where('movie_id', $movie->id)
                ->exists();
        }

        return view('movies.show', compact('movie', 'similarMovies', 'watchProgress', 'isFavorited'));
    }

    public function watch($slug)
    {
        $movie = Movie::where('slug', $slug)->firstOrFail();

        // Check if movie is still uploading
        if (!$movie->cdn_path) {
            abort(503, 'This video is still being processed. Please check back in a few minutes.');
        }

        // Check if rental expired
        if ($movie->rental_expires_at && $movie->rental_expires_at < now()) {
            abort(403, 'This content is no longer available.');
        }

        // Get available qualities
        $availableQualities = $movie->getAvailableQualities();
        
        // Get default URL (first quality or main cdn_path)
        $bunnyDomain = env('BUNNY_CDN_DOMAIN', env('BUNNY_STORAGE_HOST', 'sg.storage.bunnycdn.com'));
        $cdnPath = $movie->cdn_path;
        
        // Generate default CDN URL
        if (str_contains($bunnyDomain, 'b-cdn.net') || str_contains($bunnyDomain, 'bunnycdn.com')) {
            $url = "https://{$bunnyDomain}/{$cdnPath}";
        } else {
            $url = "https://{$bunnyDomain}/" . env('BUNNY_STORAGE_USERNAME', 'storage-movie-test') . "/{$cdnPath}";
        }
        
        // Optional: Add signed URL token for security
        $token = env('BUNNY_API_KEY', '');
        if ($token) {
            $expires = time() + 3600;
            $url .= "?token={$token}:{$expires}";
        }

        // Add tokens to all quality URLs
        $qualityUrls = [];
        foreach ($availableQualities as $quality => $qualityUrl) {
            if ($token) {
                $qualityUrls[$quality] = $qualityUrl . "?token={$token}:{$expires}";
            } else {
                $qualityUrls[$quality] = $qualityUrl;
            }
        }

        // Save/update watch history
        if (Auth::check()) {
            WatchHistory::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'movie_id' => $movie->id,
                ],
                [
                    'watched_at' => now(),
                ]
            );
        }

        return view('movies.watch', compact('movie', 'url', 'qualityUrls'));
    }

    public function updateProgress(Request $request, $slug)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $movie = Movie::where('slug', $slug)->firstOrFail();
        
        WatchHistory::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'movie_id' => $movie->id,
            ],
            [
                'progress' => $request->progress,
                'watched_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }
}
