@extends('layouts.app')

@section('title', 'My List - Cinema Bunny')

@section('content')
<div style="padding: 50px;">
    <h1 style="font-size: 36px; margin-bottom: 30px;">My List</h1>
    
    @if($favorites->count() > 0)
        <div class="movie-grid">
            @foreach($favorites as $favorite)
                @php $movie = $favorite->movie; @endphp
                <div class="movie-card" onclick="window.location='{{ route('movies.show', $movie->slug) }}'">
                    <img src="{{ $movie->thumbnail_url ?? $movie->poster_url ?? 'https://via.placeholder.com/300x450' }}" alt="{{ $movie->title }}">
                    <div class="movie-card-overlay">
                        <div class="movie-card-title">{{ $movie->title }}</div>
                        <div class="movie-card-info">
                            @if($movie->year) {{ $movie->year }} • @endif
                            @if($movie->rating) <span class="rating">★ {{ $movie->rating }}</span> @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div style="padding: 20px 0;">
            {{ $favorites->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 100px 20px;">
            <h2 style="font-size: 24px; margin-bottom: 20px; color: rgba(255, 255, 255, 0.7);">Your list is empty</h2>
            <p style="color: rgba(255, 255, 255, 0.5); margin-bottom: 30px;">Start adding movies to your list to watch them later.</p>
            <a href="{{ route('movies.index') }}" class="btn btn-primary">Browse Movies</a>
        </div>
    @endif
</div>
@endsection

