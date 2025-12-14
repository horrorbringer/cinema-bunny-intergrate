<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Genre;
use App\Models\User;
use App\Jobs\UploadVideoToBunnyJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_movies' => Movie::count(),
            'total_users' => User::count(),
            'featured_movies' => Movie::where('is_featured', true)->count(),
            'trending_movies' => Movie::where('is_trending', true)->count(),
            'recent_movies' => Movie::latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function movies()
    {
        $movies = Movie::with('genres')->latest()->paginate(20);
        return view('admin.movies.index', compact('movies'));
    }

    public function create()
    {
        $genres = Genre::all();
        return view('admin.movies.create', compact('genres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:mp4,mov,avi,mkv,webm',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'duration' => 'nullable|integer|min:1',
            'rating' => 'nullable|numeric|min:0|max:10',
            'age_rating' => 'nullable|string|in:G,PG,PG-13,R,NC-17',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
        ]);

        $file = $request->file('file');
        $uniqueId = time();
        $fileName = Str::slug($request->title) . '-' . $uniqueId . '.' . $file->getClientOriginalExtension();

        // Use async upload (RECOMMENDED) - saves locally first, uploads in background
        // This prevents timeout errors and gives immediate response
        return $this->storeAsync($request, $file, $fileName, $uniqueId);
    }

    /**
     * Async upload: Save locally first, upload in background (RECOMMENDED)
     * This prevents timeout errors - user gets immediate response
     */
    private function storeAsync(Request $request, $file, $fileName, $uniqueId)
    {
        // Save files locally first (FAST - 2-5 seconds)
        $localVideoPath = 'uploads/videos/' . $fileName;
        Storage::disk('local')->putFileAs('uploads/videos', $file, $fileName);

        // Handle thumbnail - save locally
        $localThumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = 'thumb-' . Str::slug($request->title) . '-' . $uniqueId . '.' . $thumbnail->getClientOriginalExtension();
            $localThumbnailPath = 'uploads/thumbnails/' . $thumbnailName;
            Storage::disk('local')->putFileAs('uploads/thumbnails', $thumbnail, $thumbnailName);
        }

        // Handle poster - save locally
        $localPosterPath = null;
        if ($request->hasFile('poster')) {
            $poster = $request->file('poster');
            $posterName = 'poster-' . Str::slug($request->title) . '-' . $uniqueId . '.' . $poster->getClientOriginalExtension();
            $localPosterPath = 'uploads/posters/' . $posterName;
            Storage::disk('local')->putFileAs('uploads/posters', $poster, $posterName);
        }

        // Create movie record immediately (upload will happen in background)
        $movie = Movie::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . $uniqueId,
            'description' => $request->description,
            'cdn_path' => null, // Will be set by job after upload
            'thumbnail' => null, // Will be set by job after upload
            'poster' => null, // Will be set by job after upload
            'year' => $request->year,
            'duration' => $request->duration,
            'rating' => $request->rating,
            'age_rating' => $request->age_rating,
            'is_featured' => $request->has('is_featured'),
            'is_trending' => $request->has('is_trending'),
            'rental_expires_at' => $request->has('rental_expires_at') ? Carbon::parse($request->rental_expires_at) : null,
        ]);

        // Attach genres
        if ($request->has('genres')) {
            $movie->genres()->attach($request->genres);
        }

        // Queue the upload job (runs in background - NO TIMEOUT ISSUES!)
        UploadVideoToBunnyJob::dispatch($movie, $localVideoPath, $localThumbnailPath, $localPosterPath);

        return redirect()->route('admin.movies')->with('success', 'Movie saved! Uploading to Bunny.net in background. Check back in a few minutes.');
    }

    /**
     * Direct upload: Upload immediately to Bunny.net
     * This is a workaround when queue worker isn't running
     */
    private function storeDirect(Request $request, $file, $fileName, $uniqueId)
    {
        // Increase PHP execution time for large uploads
        set_time_limit(3600); // 1 hour
        ini_set('max_execution_time', '3600');
        ini_set('memory_limit', '2048M');
        
        try {
            // Upload video directly to BunnyCDN
            // SFTP driver doesn't support putStream(), so we use put() with file content
            $filePath = $file->getRealPath();
            $content = file_get_contents($filePath);
            Storage::disk('bunny')->put($fileName, $content);
        } catch (\Exception $e) {
            \Log::error('Video upload failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Video upload failed: ' . $e->getMessage() . '. Please try again or use a smaller file.');
        }

        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = 'thumb-' . Str::slug($request->title) . '-' . $uniqueId . '.' . $thumbnail->getClientOriginalExtension();
            Storage::disk('bunny')->put($thumbnailName, file_get_contents($thumbnail->getRealPath()));
            $thumbnailPath = $thumbnailName;
        }

        // Handle poster upload
        $posterPath = null;
        if ($request->hasFile('poster')) {
            $poster = $request->file('poster');
            $posterName = 'poster-' . Str::slug($request->title) . '-' . $uniqueId . '.' . $poster->getClientOriginalExtension();
            Storage::disk('bunny')->put($posterName, file_get_contents($poster->getRealPath()));
            $posterPath = $posterName;
        }

        // Create movie record
        $movie = Movie::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . $uniqueId,
            'description' => $request->description,
            'cdn_path' => $fileName,
            'thumbnail' => $thumbnailPath,
            'poster' => $posterPath,
            'year' => $request->year,
            'duration' => $request->duration,
            'rating' => $request->rating,
            'age_rating' => $request->age_rating,
            'is_featured' => $request->has('is_featured'),
            'is_trending' => $request->has('is_trending'),
            'rental_expires_at' => $request->has('rental_expires_at') ? Carbon::parse($request->rental_expires_at) : null,
        ]);

        // Attach genres
        if ($request->has('genres')) {
            $movie->genres()->attach($request->genres);
        }

        return redirect()->route('admin.movies')->with('success', 'Movie uploaded successfully to Bunny.net!');
    }

    public function edit($id)
    {
        $movie = Movie::with('genres')->findOrFail($id);
        $genres = Genre::all();
        return view('admin.movies.edit', compact('movie', 'genres'));
    }

    public function update(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'duration' => 'nullable|integer|min:1',
            'rating' => 'nullable|numeric|min:0|max:10',
            'age_rating' => 'nullable|string|in:G,PG,PG-13,R,NC-17',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = 'thumb-' . Str::slug($request->title) . '-' . time() . '.' . $thumbnail->getClientOriginalExtension();
            Storage::disk('bunny')->put($thumbnailName, file_get_contents($thumbnail->getRealPath()));
            $movie->thumbnail = $thumbnailName;
        }

        // Handle poster upload
        if ($request->hasFile('poster')) {
            $poster = $request->file('poster');
            $posterName = 'poster-' . Str::slug($request->title) . '-' . time() . '.' . $poster->getClientOriginalExtension();
            Storage::disk('bunny')->put($posterName, file_get_contents($poster->getRealPath()));
            $movie->poster = $posterName;
        }

        $movie->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . $movie->id,
            'description' => $request->description,
            'year' => $request->year,
            'duration' => $request->duration,
            'rating' => $request->rating,
            'age_rating' => $request->age_rating,
            'is_featured' => $request->has('is_featured'),
            'is_trending' => $request->has('is_trending'),
            'rental_expires_at' => $request->has('rental_expires_at') ? Carbon::parse($request->rental_expires_at) : null,
        ]);

        // Sync genres
        if ($request->has('genres')) {
            $movie->genres()->sync($request->genres);
        } else {
            $movie->genres()->detach();
        }

        return redirect()->route('admin.movies')->with('success', 'Movie updated successfully!');
    }

    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);
        $movie->delete();

        return redirect()->route('admin.movies')->with('success', 'Movie deleted successfully!');
    }

    /**
     * Add additional quality version to existing movie
     */
    public function addQuality(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        $request->validate([
            'quality_file' => 'required|file|mimes:mp4,mov,avi,mkv,webm',
            'quality' => 'required|string|in:1080p,720p,480p,360p,240p',
        ]);

        $file = $request->file('quality_file');
        $quality = $request->input('quality');
        $fileName = Str::slug($movie->title) . '-' . $quality . '-' . time() . '.' . $file->getClientOriginalExtension();

        // Save locally first
        $localVideoPath = 'uploads/videos/' . $fileName;
        Storage::disk('local')->putFileAs('uploads/videos', $file, $fileName);

        // Queue upload job
        UploadVideoToBunnyJob::dispatch($movie, $localVideoPath, null, null, $quality, $fileName);

        // Update video_qualities JSON
        $qualities = $movie->video_qualities ?? [];
        $qualities[$quality] = $fileName; // Will be updated by job after upload
        $movie->video_qualities = $qualities;
        $movie->save();

        return redirect()->route('admin.movies.edit', $movie->id)
            ->with('success', "{$quality} quality version is being uploaded. Check back in a few minutes.");
    }

    /**
     * Remove a quality version
     */
    public function removeQuality($id, $quality)
    {
        $movie = Movie::findOrFail($id);
        $qualities = $movie->video_qualities ?? [];

        if (isset($qualities[$quality])) {
            // Delete file from Bunny.net (optional - you can keep files for backup)
            // $bunnyService = new \App\Services\BunnyStorageService();
            // $bunnyService->deleteFile($qualities[$quality]);

            unset($qualities[$quality]);
            $movie->video_qualities = $qualities;
            $movie->save();
        }

        return redirect()->route('admin.movies.edit', $movie->id)
            ->with('success', "{$quality} quality version removed.");
    }
}
