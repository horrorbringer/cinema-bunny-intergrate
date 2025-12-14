<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Movie;

class VideoTestController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:mp4,mov,avi',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'duration' => 'nullable|integer|min:1',
            'rating' => 'nullable|numeric|min:0|max:10',
            'age_rating' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $file = $request->file('file');
        $fileName = Str::slug($request->title) . '-' . time() . '.' . $file->getClientOriginalExtension();

        // Upload video to BunnyCDN
        Storage::disk('bunny')->put($fileName, file_get_contents($file->getRealPath()));

        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = 'thumb-' . Str::slug($request->title) . '-' . time() . '.' . $thumbnail->getClientOriginalExtension();
            Storage::disk('bunny')->put($thumbnailName, file_get_contents($thumbnail->getRealPath()));
            $thumbnailPath = $thumbnailName;
        }

        // Handle poster upload
        $posterPath = null;
        if ($request->hasFile('poster')) {
            $poster = $request->file('poster');
            $posterName = 'poster-' . Str::slug($request->title) . '-' . time() . '.' . $poster->getClientOriginalExtension();
            Storage::disk('bunny')->put($posterName, file_get_contents($poster->getRealPath()));
            $posterPath = $posterName;
        }

        // Save in database
        $movie = Movie::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(),
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
            'rental_expires_at' => Carbon::now()->addDays(7), // 7-day expiry
        ]);

        return back()->with('success', 'Movie uploaded successfully! ID: ' . $movie->id);
    }

    public function watch($id)
    {
        $movie = Movie::findOrFail($id);

        // Check if rental expired
        if ($movie->rental_expires_at < now()) {
            abort(403, 'Rental expired.');
        }

        // Generate signed URL for BunnyCDN (example: 1 hour)
        $expires = time() + 3600; // 1 hour
        $url = "https://" . env('BUNNY_CDN_DOMAIN') . "/" . $movie->cdn_path . "?token=" . env('BUNNY_API_KEY') . ":" . $expires;

        return view('watch', compact('url', 'movie'));
    }
}
