@extends('layouts.app')

@section('title', 'Admin Dashboard - Cinema Bunny')

@section('content')
<div style="padding: 50px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
        <h1 style="font-size: 36px;">Admin Dashboard</h1>
        <a href="{{ route('admin.movies.create') }}" class="btn btn-primary">+ Upload New Movie</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div style="background: rgba(255, 255, 255, 0.05); padding: 30px; border-radius: 8px;">
            <h3 style="font-size: 14px; color: rgba(255, 255, 255, 0.7); margin-bottom: 10px;">Total Movies</h3>
            <p style="font-size: 48px; font-weight: bold; color: #e50914;">{{ $stats['total_movies'] }}</p>
        </div>
        <div style="background: rgba(255, 255, 255, 0.05); padding: 30px; border-radius: 8px;">
            <h3 style="font-size: 14px; color: rgba(255, 255, 255, 0.7); margin-bottom: 10px;">Total Users</h3>
            <p style="font-size: 48px; font-weight: bold; color: #e50914;">{{ $stats['total_users'] }}</p>
        </div>
        <div style="background: rgba(255, 255, 255, 0.05); padding: 30px; border-radius: 8px;">
            <h3 style="font-size: 14px; color: rgba(255, 255, 255, 0.7); margin-bottom: 10px;">Featured Movies</h3>
            <p style="font-size: 48px; font-weight: bold; color: #e50914;">{{ $stats['featured_movies'] }}</p>
        </div>
        <div style="background: rgba(255, 255, 255, 0.05); padding: 30px; border-radius: 8px;">
            <h3 style="font-size: 14px; color: rgba(255, 255, 255, 0.7); margin-bottom: 10px;">Trending Movies</h3>
            <p style="font-size: 48px; font-weight: bold; color: #e50914;">{{ $stats['trending_movies'] }}</p>
        </div>
    </div>

    <div style="background: rgba(255, 255, 255, 0.05); padding: 30px; border-radius: 8px;">
        <h2 style="font-size: 24px; margin-bottom: 20px;">Recent Movies</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
            @forelse($stats['recent_movies'] as $movie)
                <div style="cursor: pointer;" onclick="window.location='{{ route('admin.movies.edit', $movie->id) }}'">
                    <img src="{{ $movie->thumbnail_url ?? $movie->poster_url ?? 'https://via.placeholder.com/300x450' }}" 
                         alt="{{ $movie->title }}" 
                         style="width: 100%; height: 300px; object-fit: cover; border-radius: 8px; margin-bottom: 10px;">
                    <p style="font-weight: bold;">{{ $movie->title }}</p>
                </div>
            @empty
                <p>No movies yet. <a href="{{ route('admin.movies.create') }}">Upload your first movie!</a></p>
            @endforelse
        </div>
    </div>

    <div style="margin-top: 30px;">
        <a href="{{ route('admin.movies') }}" class="btn btn-secondary">Manage All Movies →</a>
    </div>
</div>
@endsection

