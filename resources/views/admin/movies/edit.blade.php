@extends('layouts.app')

@section('title', 'Edit Movie - Admin')

@section('content')
<div style="padding: 50px; max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <a href="{{ route('admin.movies') }}" style="color: #ffffff; text-decoration: none;">← Back to Movies</a>
        <h1 style="font-size: 36px; margin-top: 20px;">Edit Movie: {{ $movie->title }}</h1>
    </div>

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.movies.update', $movie->id) }}" method="POST" enctype="multipart/form-data" style="background: rgba(255, 255, 255, 0.05); padding: 40px; border-radius: 8px;">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="title">Movie Title *</label>
                <input type="text" id="title" name="title" value="{{ old('title', $movie->title) }}" required>
            </div>

            <div class="form-group">
                <label for="year">Year</label>
                <input type="number" id="year" name="year" value="{{ old('year', $movie->year) }}" min="1900" max="{{ date('Y') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5" style="width: 100%; padding: 12px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 4px; color: #ffffff; font-size: 16px; font-family: inherit;">{{ old('description', $movie->description) }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="duration">Duration (minutes)</label>
                <input type="number" id="duration" name="duration" value="{{ old('duration', $movie->duration) }}" min="1">
            </div>

            <div class="form-group">
                <label for="rating">Rating (0-10)</label>
                <input type="number" id="rating" name="rating" value="{{ old('rating', $movie->rating) }}" min="0" max="10" step="0.1">
            </div>

            <div class="form-group">
                <label for="age_rating">Age Rating</label>
                <select id="age_rating" name="age_rating" style="width: 100%; padding: 12px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 4px; color: #ffffff; font-size: 16px;">
                    <option value="">Select...</option>
                    <option value="G" {{ old('age_rating', $movie->age_rating) == 'G' ? 'selected' : '' }}>G</option>
                    <option value="PG" {{ old('age_rating', $movie->age_rating) == 'PG' ? 'selected' : '' }}>PG</option>
                    <option value="PG-13" {{ old('age_rating', $movie->age_rating) == 'PG-13' ? 'selected' : '' }}>PG-13</option>
                    <option value="R" {{ old('age_rating', $movie->age_rating) == 'R' ? 'selected' : '' }}>R</option>
                    <option value="NC-17" {{ old('age_rating', $movie->age_rating) == 'NC-17' ? 'selected' : '' }}>NC-17</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="genres">Genres</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin-top: 10px;">
                @foreach($genres as $genre)
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="genres[]" value="{{ $genre->id }}" {{ $movie->genres->contains($genre->id) ? 'checked' : '' }}>
                        <span>{{ $genre->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="thumbnail">Thumbnail Image</label>
                @if($movie->thumbnail)
                    <div style="margin-bottom: 10px;">
                        <img src="{{ $movie->thumbnail }}" alt="Current thumbnail" style="width: 150px; height: 225px; object-fit: cover; border-radius: 4px;">
                    </div>
                @endif
                <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
                <small style="color: rgba(255, 255, 255, 0.6); display: block; margin-top: 5px;">Leave empty to keep current</small>
            </div>

            <div class="form-group">
                <label for="poster">Poster Image</label>
                @if($movie->poster)
                    <div style="margin-bottom: 10px;">
                        <img src="{{ $movie->poster }}" alt="Current poster" style="width: 150px; height: 225px; object-fit: cover; border-radius: 4px;">
                    </div>
                @endif
                <input type="file" id="poster" name="poster" accept="image/*">
                <small style="color: rgba(255, 255, 255, 0.6); display: block; margin-top: 5px;">Leave empty to keep current</small>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group" style="display: flex; gap: 10px; align-items: center;">
                <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $movie->is_featured) ? 'checked' : '' }}>
                <label for="is_featured" style="margin: 0;">Featured Movie</label>
            </div>

            <div class="form-group" style="display: flex; gap: 10px; align-items: center;">
                <input type="checkbox" id="is_trending" name="is_trending" value="1" {{ old('is_trending', $movie->is_trending) ? 'checked' : '' }}>
                <label for="is_trending" style="margin: 0;">Trending Movie</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 30px;">Update Movie</button>
    </form>

    <!-- Add Quality Version Section -->
    <div style="background: rgba(255, 255, 255, 0.05); padding: 40px; border-radius: 8px; margin-top: 40px;">
        <h2 style="font-size: 24px; margin-bottom: 20px;">📹 Video Quality Versions</h2>
        
        <!-- Current Qualities -->
        @php
            $qualities = $movie->video_qualities ?? [];
            if ($movie->cdn_path) {
                $qualities['auto'] = $movie->cdn_path; // Main video
            }
        @endphp
        
        @if(!empty($qualities))
            <div style="margin-bottom: 30px;">
                <h3 style="font-size: 18px; margin-bottom: 15px; color: rgba(255, 255, 255, 0.8);">Current Qualities:</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    @foreach($qualities as $quality => $path)
                        <div style="background: rgba(229, 9, 20, 0.2); padding: 10px 15px; border-radius: 6px; display: flex; align-items: center; gap: 10px;">
                            <span style="font-weight: 600;">{{ $quality === 'auto' ? 'Main (1080p)' : strtoupper($quality) }}</span>
                            @if($quality !== 'auto')
                                <form action="{{ route('admin.movies.remove-quality', [$movie->id, $quality]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Remove {{ $quality }} quality?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: rgba(255, 0, 0, 0.3); border: none; color: #fff; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 12px;">Remove</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Add New Quality Form -->
        <form action="{{ route('admin.movies.add-quality', $movie->id) }}" method="POST" enctype="multipart/form-data" style="border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px;">
            @csrf
            <h3 style="font-size: 18px; margin-bottom: 15px; color: rgba(255, 255, 255, 0.8);">Add Quality Version:</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="quality">Quality</label>
                    <select id="quality" name="quality" required style="width: 100%; padding: 12px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 4px; color: #ffffff; font-size: 16px;">
                        <option value="">Select Quality...</option>
                        <option value="720p" {{ in_array('720p', array_keys($qualities)) ? 'disabled' : '' }}>720p HD</option>
                        <option value="480p" {{ in_array('480p', array_keys($qualities)) ? 'disabled' : '' }}>480p SD</option>
                        <option value="360p" {{ in_array('360p', array_keys($qualities)) ? 'disabled' : '' }}>360p</option>
                        <option value="240p" {{ in_array('240p', array_keys($qualities)) ? 'disabled' : '' }}>240p</option>
                    </select>
                    <small style="color: rgba(255, 255, 255, 0.6); display: block; margin-top: 5px;">💡 Tip: Use HandBrake (free) to create lower quality versions from your 1080p video</small>
                </div>

                <div class="form-group">
                    <label for="quality_file">Video File</label>
                    <input type="file" id="quality_file" name="quality_file" accept="video/*" required style="width: 100%; padding: 12px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 4px; color: #ffffff; font-size: 16px;">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Upload Quality Version</button>
        </form>
    </div>
</div>
@endsection

