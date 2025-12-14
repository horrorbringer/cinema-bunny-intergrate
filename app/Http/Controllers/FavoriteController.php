<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function toggle($slug)
    {
        $movie = Movie::where('slug', $slug)->firstOrFail();
        
        $favorite = Favorite::where('user_id', Auth::id())
            ->where('movie_id', $movie->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['favorited' => false]);
        } else {
            Favorite::create([
                'user_id' => Auth::id(),
                'movie_id' => $movie->id,
            ]);
            return response()->json(['favorited' => true]);
        }
    }

    public function index()
    {
        $favorites = Favorite::where('user_id', Auth::id())
            ->with('movie.genres')
            ->latest()
            ->paginate(20);

        return view('favorites.index', compact('favorites'));
    }
}
