@extends('layouts.app')

@section('title', $movie->title . ' - Cinema Bunny')

@push('styles')
<style>
    .movie-detail {
        padding: 50px;
        display: flex;
        gap: 40px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .movie-poster {
        flex-shrink: 0;
    }

    .movie-poster img {
        width: 350px;
        height: 525px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.5);
    }

    .movie-info {
        flex: 1;
    }

    .movie-title {
        font-size: 48px;
        margin-bottom: 20px;
    }

    .movie-meta {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .movie-meta-item {
        color: rgba(255, 255, 255, 0.7);
    }

    .movie-description {
        font-size: 18px;
        line-height: 1.8;
        margin-bottom: 30px;
        color: rgba(255, 255, 255, 0.9);
    }

    .movie-actions {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
    }

    .btn-large {
        padding: 15px 30px;
        font-size: 18px;
    }

    .genres {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .genre-tag {
        background: rgba(255, 255, 255, 0.2);
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 14px;
    }

    .similar-movies {
        padding: 50px;
    }

    .favorite-btn {
        background: transparent;
        border: 2px solid #ffffff;
        color: #ffffff;
        padding: 15px 30px;
        font-size: 18px;
        cursor: pointer;
        border-radius: 4px;
        transition: all 0.3s;
    }

    .favorite-btn:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .favorite-btn.favorited {
        background: #e50914;
        border-color: #e50914;
    }
</style>
@endpush

@section('content')
<div class="movie-detail">
    <div class="movie-poster">
        <img src="{{ $movie->poster_url ?? $movie->thumbnail_url ?? 'https://via.placeholder.com/350x525' }}" alt="{{ $movie->title }}">
    </div>
    <div class="movie-info">
        <h1 class="movie-title">{{ $movie->title }}</h1>
        <div class="movie-meta">
            @if($movie->year)
                <span class="movie-meta-item">{{ $movie->year }}</span>
            @endif
            @if($movie->duration)
                <span class="movie-meta-item">{{ $movie->duration }} min</span>
            @endif
            @if($movie->age_rating)
                <span class="movie-meta-item">{{ $movie->age_rating }}</span>
            @endif
            @if($movie->rating)
                <span class="movie-meta-item rating">★ {{ $movie->rating }}/10</span>
            @endif
        </div>
        @if($movie->genres->count() > 0)
            <div class="genres">
                @foreach($movie->genres as $genre)
                    <span class="genre-tag">{{ $genre->name }}</span>
                @endforeach
            </div>
        @endif
        <p class="movie-description">{{ $movie->description ?? 'No description available.' }}</p>
        <div class="movie-actions">
            <a href="{{ route('movies.watch', $movie->slug) }}" class="btn btn-primary btn-large">▶ Play</a>
            @auth
                <button class="favorite-btn {{ $isFavorited ? 'favorited' : '' }}" id="favoriteBtn" data-slug="{{ $movie->slug }}">
                    <span id="favoriteText">{{ $isFavorited ? '✓ In My List' : '+ My List' }}</span>
                </button>
            @endauth
        </div>
        @if($watchProgress > 0)
            <div style="margin-top: 20px;">
                <p>Continue watching from {{ gmdate('H:i:s', $watchProgress) }}</p>
            </div>
        @endif
    </div>
</div>

@if($similarMovies->count() > 0)
    <div class="similar-movies">
        <h2 class="section-title">More Like This</h2>
        <div class="movie-row">
            @foreach($similarMovies as $similar)
                <div class="movie-item" onclick="window.location='{{ route('movies.show', $similar->slug) }}'">
                    <img src="{{ $similar->thumbnail_url ?? $similar->poster_url ?? 'https://via.placeholder.com/300x450' }}" alt="{{ $similar->title }}">
                </div>
            @endforeach
        </div>
    </div>
@endif

@auth
@push('scripts')
<script>
    // Toggle favorite
    document.getElementById('favoriteBtn')?.addEventListener('click', function() {
        const slug = this.dataset.slug;
        fetch(`/favorite/${slug}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.favorited) {
                this.classList.add('favorited');
                document.getElementById('favoriteText').textContent = '✓ In My List';
            } else {
                this.classList.remove('favorited');
                document.getElementById('favoriteText').textContent = '+ My List';
            }
        });
    });
</script>
@endpush
@endauth
@endsection

