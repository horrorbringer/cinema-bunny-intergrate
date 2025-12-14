@extends('layouts.app')

@section('title', 'Upload Video - Cinema Bunny')

@section('content')
<div style="padding: 50px; max-width: 800px; margin: 0 auto;">
    <h1 style="font-size: 36px; margin-bottom: 30px;">Upload Movie</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('video.upload') }}" method="POST" enctype="multipart/form-data" style="background: rgba(255, 255, 255, 0.05); padding: 40px; border-radius: 8px;">
        @csrf
        
        <div class="form-group">
            <label for="title">Movie Title *</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5" style="width: 100%; padding: 12px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 4px; color: #ffffff; font-size: 16px; font-family: inherit;">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label for="year">Year</label>
            <input type="number" id="year" name="year" value="{{ old('year') }}" min="1900" max="{{ date('Y') }}">
        </div>

        <div class="form-group">
            <label for="duration">Duration (minutes)</label>
            <input type="number" id="duration" name="duration" value="{{ old('duration') }}" min="1">
        </div>

        <div class="form-group">
            <label for="rating">Rating (0-10)</label>
            <input type="number" id="rating" name="rating" value="{{ old('rating') }}" min="0" max="10" step="0.1">
        </div>

        <div class="form-group">
            <label for="age_rating">Age Rating</label>
            <select id="age_rating" name="age_rating" style="width: 100%; padding: 12px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 4px; color: #ffffff; font-size: 16px;">
                <option value="">Select...</option>
                <option value="G">G</option>
                <option value="PG">PG</option>
                <option value="PG-13">PG-13</option>
                <option value="R">R</option>
                <option value="NC-17">NC-17</option>
            </select>
        </div>

        <div class="form-group">
            <label for="file">Video File *</label>
            <input type="file" id="file" name="file" accept="video/*" required>
        </div>

        <div class="form-group">
            <label for="thumbnail">Thumbnail Image</label>
            <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
        </div>

        <div class="form-group">
            <label for="poster">Poster Image</label>
            <input type="file" id="poster" name="poster" accept="image/*">
        </div>

        <div class="form-group" style="display: flex; gap: 10px; align-items: center;">
            <input type="checkbox" id="is_featured" name="is_featured" value="1" style="width: auto;">
            <label for="is_featured" style="margin: 0;">Featured</label>
        </div>

        <div class="form-group" style="display: flex; gap: 10px; align-items: center;">
            <input type="checkbox" id="is_trending" name="is_trending" value="1" style="width: auto;">
            <label for="is_trending" style="margin: 0;">Trending</label>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">Upload Movie</button>
    </form>
</div>
@endsection
