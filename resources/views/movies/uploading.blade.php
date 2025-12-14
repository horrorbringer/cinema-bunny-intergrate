@extends('layouts.app')

@section('title', $movie->title . ' - Uploading')

@section('content')
<div style="padding: 100px 50px; text-align: center;">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="font-size: 64px; margin-bottom: 30px;">⏳</div>
        <h1 style="font-size: 36px; margin-bottom: 20px;">Video is Still Uploading</h1>
        <p style="font-size: 18px; color: rgba(255, 255, 255, 0.7); margin-bottom: 30px;">
            {{ $movie->title }} is currently being processed and uploaded to our servers.
        </p>
        <p style="font-size: 16px; color: rgba(255, 255, 255, 0.5); margin-bottom: 40px;">
            This usually takes a few minutes depending on the file size. Please check back shortly!
        </p>
        <div style="display: flex; gap: 15px; justify-content: center;">
            <a href="{{ route('movies.index') }}" class="btn btn-secondary">Browse Other Movies</a>
            <button onclick="location.reload()" class="btn btn-primary">Refresh Page</button>
        </div>
    </div>
</div>
@endsection

