@extends('layouts.app')

@section('title', 'Manage Movies - Admin')

@section('content')
<div style="padding: 50px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="font-size: 36px;">Manage Movies</h1>
        <a href="{{ route('admin.movies.create') }}" class="btn btn-primary">+ Upload New Movie</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background: rgba(255, 255, 255, 0.05); padding: 30px; border-radius: 8px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                    <th style="padding: 15px; text-align: left;">Thumbnail</th>
                    <th style="padding: 15px; text-align: left;">Title</th>
                    <th style="padding: 15px; text-align: left;">Year</th>
                    <th style="padding: 15px; text-align: left;">Rating</th>
                    <th style="padding: 15px; text-align: left;">Status</th>
                    <th style="padding: 15px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movies as $movie)
                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                        <td style="padding: 15px;">
                            <img src="{{ $movie->thumbnail_url ?? $movie->poster_url ?? 'https://via.placeholder.com/100x150' }}" 
                                 alt="{{ $movie->title }}" 
                                 style="width: 80px; height: 120px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td style="padding: 15px;">
                            <strong>{{ $movie->title }}</strong><br>
                            <small style="color: rgba(255, 255, 255, 0.6);">{{ Str::limit($movie->description, 50) }}</small>
                        </td>
                        <td style="padding: 15px;">{{ $movie->year ?? 'N/A' }}</td>
                        <td style="padding: 15px;">
                            @if($movie->rating)
                                <span class="rating">★ {{ $movie->rating }}/10</span>
                            @else
                                N/A
                            @endif
                        </td>
                        <td style="padding: 15px;">
                            @if($movie->is_featured)
                                <span style="background: #e50914; padding: 5px 10px; border-radius: 4px; font-size: 12px;">Featured</span>
                            @endif
                            @if($movie->is_trending)
                                <span style="background: #28a745; padding: 5px 10px; border-radius: 4px; font-size: 12px; margin-left: 5px;">Trending</span>
                            @endif
                        </td>
                        <td style="padding: 15px;">
                            <div style="display: flex; gap: 10px;">
                                <a href="{{ route('movies.show', $movie->slug) }}" class="btn btn-secondary" style="padding: 5px 15px; font-size: 14px;">View</a>
                                <a href="{{ route('admin.movies.edit', $movie->id) }}" class="btn btn-secondary" style="padding: 5px 15px; font-size: 14px;">Edit</a>
                                <form action="{{ route('admin.movies.destroy', $movie->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary" style="padding: 5px 15px; font-size: 14px; background: #dc3545;" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 40px; text-align: center; color: rgba(255, 255, 255, 0.6);">
                            No movies found. <a href="{{ route('admin.movies.create') }}">Upload your first movie!</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 30px;">
            {{ $movies->links() }}
        </div>
    </div>
</div>
@endsection

