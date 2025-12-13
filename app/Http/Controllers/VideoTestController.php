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
        ]);

        $file = $request->file('file');
        $fileName = Str::slug($request->title) . '-' . time() . '.' . $file->getClientOriginalExtension();

        // Upload to BunnyCDN
        Storage::disk('bunny')->put($fileName, file_get_contents($file->getRealPath()));

        // Save in database
        $movie = Movie::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'cdn_path' => $fileName,
            'rental_expires_at' => Carbon::now()->addDays(7), // 7-day expiry
        ]);

        return back()->with('success', 'Uploaded! Movie ID: ' . $movie->id);
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
