@extends('layouts.app')

@section('title', 'Cinema Bunny - Stream Movies & TV Shows')

@push('styles')
<style>
    /* Hero Section */
    .hero-section {
        height: 85vh;
        min-height: 500px;
        position: relative;
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: flex-end;
        padding: 0 50px 80px;
        margin-bottom: 60px;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top, rgba(20,20,20,0.95) 0%, rgba(20,20,20,0.7) 50%, rgba(20,20,20,0.3) 100%);
        z-index: 1;
    }

    .hero-content {
        max-width: 650px;
        z-index: 2;
        position: relative;
    }

    .hero-title {
        font-size: 72px;
        font-weight: 900;
        margin-bottom: 20px;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.9);
        line-height: 1.1;
        letter-spacing: -1px;
    }

    .hero-description {
        font-size: 20px;
        margin-bottom: 30px;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.95);
        text-shadow: 1px 1px 4px rgba(0,0,0,0.8);
        max-width: 550px;
    }

    .hero-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 14px 32px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 18px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: #ffffff;
        color: #000000;
    }

    .btn-primary:hover {
        background: rgba(255, 255, 255, 0.85);
        transform: scale(1.05);
    }

    .btn-secondary {
        background: rgba(109, 109, 110, 0.7);
        color: #ffffff;
        backdrop-filter: blur(10px);
    }

    .btn-secondary:hover {
        background: rgba(109, 109, 110, 0.9);
        transform: scale(1.05);
    }

    /* Section Titles */
    .section-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 20px;
        padding-left: 50px;
        color: #ffffff;
        letter-spacing: -0.5px;
    }

    /* Movie Row (Horizontal Scrolling) */
    .movie-row {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        overflow-y: hidden;
        padding: 10px 50px 30px;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }

    .movie-row::-webkit-scrollbar {
        height: 6px;
    }

    .movie-row::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 3px;
    }

    .movie-row::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }

    .movie-row::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .movie-item {
        min-width: 220px;
        width: 220px;
        cursor: pointer;
        transition: transform 0.3s ease, z-index 0.3s;
        position: relative;
    }

    .movie-item:hover {
        transform: scale(1.15) translateY(-10px);
        z-index: 10;
    }

    .movie-item img {
        width: 100%;
        height: 330px;
        object-fit: cover;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        transition: box-shadow 0.3s ease;
    }

    .movie-item:hover img {
        box-shadow: 0 8px 24px rgba(0,0,0,0.6);
    }

    /* Movie Grid (All Movies) */
    .movie-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 24px;
        padding: 0 50px 50px;
        margin-bottom: 50px;
    }

    .movie-card {
        position: relative;
        cursor: pointer;
        transition: transform 0.3s ease, z-index 0.3s;
        border-radius: 8px;
        overflow: hidden;
        background: #1a1a1a;
    }

    .movie-card:hover {
        transform: scale(1.08) translateY(-8px);
        z-index: 10;
    }

    .movie-card img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        display: block;
        transition: opacity 0.3s ease;
    }

    .movie-card:hover img {
        opacity: 0.85;
    }

    .movie-card-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.7) 60%, transparent 100%);
        padding: 24px 16px 16px;
        opacity: 0;
        transition: opacity 0.3s ease;
        transform: translateY(10px);
    }

    .movie-card:hover .movie-card-overlay {
        opacity: 1;
        transform: translateY(0);
    }

    .movie-card-title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 6px;
        color: #ffffff;
        line-height: 1.3;
    }

    .movie-card-info {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .rating {
        color: #f5c518;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Empty State */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 80px 20px;
        color: rgba(255, 255, 255, 0.6);
    }

    .empty-state h3 {
        font-size: 24px;
        margin-bottom: 10px;
        color: rgba(255, 255, 255, 0.8);
    }

    /* Pagination */
    .pagination-wrapper {
        padding: 30px 50px 50px;
        display: flex;
        justify-content: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero-section {
            height: 60vh;
            padding: 0 20px 40px;
        }

        .hero-title {
            font-size: 36px;
        }

        .hero-description {
            font-size: 16px;
        }

        .section-title {
            font-size: 22px;
            padding-left: 20px;
        }

        .movie-row {
            padding: 10px 20px 30px;
        }

        .movie-item {
            min-width: 160px;
            width: 160px;
        }

        .movie-item img {
            height: 240px;
        }

        .movie-grid {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 16px;
            padding: 0 20px 30px;
        }

        .movie-card img {
            height: 210px;
        }

        .btn {
            padding: 12px 24px;
            font-size: 16px;
        }
    }

    /* Loading Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .movie-item,
    .movie-card {
        animation: fadeIn 0.5s ease forwards;
    }

    .movie-item:nth-child(1) { animation-delay: 0.1s; }
    .movie-item:nth-child(2) { animation-delay: 0.2s; }
    .movie-item:nth-child(3) { animation-delay: 0.3s; }
    .movie-item:nth-child(4) { animation-delay: 0.4s; }
    .movie-item:nth-child(5) { animation-delay: 0.5s; }
</style>
@endpush

@section('content')
@if($featured->count() > 0)
    @php $featuredMovie = $featured->first(); @endphp
    <div class="hero-section" style="background-image: url('{{ $featuredMovie->poster_url ?? $featuredMovie->thumbnail_url ?? 'https://via.placeholder.com/1920x1080' }}');">
        <div class="hero-content">
            <h1 class="hero-title">{{ $featuredMovie->title }}</h1>
            <p class="hero-description">{{ Str::limit($featuredMovie->description ?? 'Watch now on Cinema Bunny', 180) }}</p>
            <div class="hero-buttons">
                <a href="{{ route('movies.watch', $featuredMovie->slug) }}" class="btn btn-primary">
                    <span>▶</span> Play
                </a>
                <a href="{{ route('movies.show', $featuredMovie->slug) }}" class="btn btn-secondary">
                    <span>ℹ</span> More Info
                </a>
            </div>
        </div>
    </div>
@endif

@if($trending->count() > 0)
    <h2 class="section-title">Trending Now</h2>
    <div class="movie-row">
        @foreach($trending as $movie)
            <div class="movie-item" onclick="window.location='{{ route('movies.show', $movie->slug) }}'">
                <img src="{{ $movie->thumbnail_url ?? $movie->poster_url ?? 'https://via.placeholder.com/300x450' }}" 
                     alt="{{ $movie->title }}"
                     loading="lazy">
            </div>
        @endforeach
    </div>
@endif

@if($featured->count() > 1)
    <h2 class="section-title">Featured</h2>
    <div class="movie-row">
        @foreach($featured->skip(1) as $movie)
            <div class="movie-item" onclick="window.location='{{ route('movies.show', $movie->slug) }}'">
                <img src="{{ $movie->thumbnail_url ?? $movie->poster_url ?? 'https://via.placeholder.com/300x450' }}" 
                     alt="{{ $movie->title }}"
                     loading="lazy">
            </div>
        @endforeach
    </div>
@endif

@if($genres->count() > 0)
    @foreach($genres as $genre)
        @php
            $genreMovies = \App\Models\Movie::whereHas('genres', function($q) use ($genre) {
                $q->where('genres.id', $genre->id);
            })->whereNotNull('cdn_path')->take(10)->get();
        @endphp
        @if($genreMovies->count() > 0)
            <h2 class="section-title">{{ $genre->name }}</h2>
            <div class="movie-row">
                @foreach($genreMovies as $movie)
                    <div class="movie-item" onclick="window.location='{{ route('movies.show', $movie->slug) }}'">
                        <img src="{{ $movie->thumbnail_url ?? $movie->poster_url ?? 'https://via.placeholder.com/300x450' }}" 
                             alt="{{ $movie->title }}"
                             loading="lazy">
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach
@endif

<h2 class="section-title">All Movies</h2>
<div class="movie-grid">
    @forelse($movies as $movie)
        <div class="movie-card" onclick="window.location='{{ route('movies.show', $movie->slug) }}'">
            <img src="{{ $movie->thumbnail_url ?? $movie->poster_url ?? 'https://via.placeholder.com/300x450' }}" 
                 alt="{{ $movie->title }}"
                 loading="lazy">
            <div class="movie-card-overlay">
                <div class="movie-card-title">{{ $movie->title }}</div>
                <div class="movie-card-info">
                    @if($movie->year)
                        <span>{{ $movie->year }}</span>
                    @endif
                    @if($movie->year && $movie->rating)
                        <span>•</span>
                    @endif
                    @if($movie->rating)
                        <span class="rating">★ {{ $movie->rating }}/10</span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <h3>No movies found</h3>
            <p>Check back later for new content!</p>
        </div>
    @endforelse
</div>

@if($movies->hasPages())
    <div class="pagination-wrapper">
        {{ $movies->links() }}
    </div>
@endif
@endsection
