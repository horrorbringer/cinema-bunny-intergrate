@extends('layouts.app')

@section('title', 'Upload Progress - ' . $movie->title)

@section('content')
<style>
    .upload-progress-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 50px 20px;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
    }

    .progress-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 50px;
        max-width: 700px;
        width: 100%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .progress-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .progress-header h1 {
        font-size: 32px;
        margin-bottom: 10px;
        color: #ffffff;
    }

    .progress-header p {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.7);
    }

    .movie-title {
        font-size: 24px;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 30px;
        text-align: center;
    }

    .progress-bar-container {
        margin-bottom: 40px;
    }

    .progress-bar-wrapper {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        height: 12px;
        overflow: hidden;
        position: relative;
        margin-bottom: 10px;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #e50914 0%, #ff6b6b 100%);
        border-radius: 10px;
        transition: width 0.5s ease;
        position: relative;
        overflow: hidden;
    }

    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.3),
            transparent
        );
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .progress-percentage {
        text-align: center;
        font-size: 28px;
        font-weight: 700;
        color: #e50914;
        margin-bottom: 5px;
    }

    .progress-text {
        text-align: center;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.6);
    }

    .upload-steps {
        margin-bottom: 30px;
    }

    .upload-step {
        display: flex;
        align-items: center;
        padding: 20px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
        margin-bottom: 15px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease;
    }

    .upload-step.completed {
        background: rgba(229, 9, 20, 0.1);
        border-color: rgba(229, 9, 20, 0.3);
    }

    .upload-step.in-progress {
        background: rgba(229, 9, 20, 0.15);
        border-color: rgba(229, 9, 20, 0.5);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        font-size: 20px;
        flex-shrink: 0;
    }

    .step-icon.pending {
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.4);
    }

    .step-icon.in-progress {
        background: #e50914;
        color: #ffffff;
        animation: spin 1s linear infinite;
    }

    .step-icon.completed {
        background: #28a745;
        color: #ffffff;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .step-content {
        flex: 1;
    }

    .step-title {
        font-size: 16px;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 5px;
    }

    .step-status {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.6);
    }

    .status-message {
        text-align: center;
        padding: 20px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        margin-bottom: 30px;
        font-size: 16px;
        color: rgba(255, 255, 255, 0.8);
    }

    .status-message.success {
        background: rgba(40, 167, 69, 0.2);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.3);
    }

    .status-message.error {
        background: rgba(220, 53, 69, 0.2);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.3);
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 30px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: #e50914;
        color: #ffffff;
    }

    .btn-primary:hover {
        background: #b8070f;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(229, 9, 20, 0.4);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-right: 8px;
        vertical-align: middle;
    }
</style>

<div class="upload-progress-container">
    <div class="progress-card">
        <div class="progress-header">
            <h1>📤 Uploading to Bunny.net</h1>
            <p>Your movie is being uploaded in the background</p>
        </div>

        <div class="movie-title">{{ $movie->title }}</div>

        <div class="progress-bar-container">
            <div class="progress-percentage" id="progressPercentage">0%</div>
            <div class="progress-text" id="progressText">Initializing upload...</div>
            <div class="progress-bar-wrapper">
                <div class="progress-bar" id="progressBar" style="width: 0%"></div>
            </div>
        </div>

        <div class="status-message" id="statusMessage" style="display: none;"></div>

        <div class="upload-steps">
            <div class="upload-step" id="stepVideo">
                <div class="step-icon pending">📹</div>
                <div class="step-content">
                    <div class="step-title">Video File</div>
                    <div class="step-status">Waiting...</div>
                </div>
            </div>

            <div class="upload-step" id="stepThumbnail">
                <div class="step-icon pending">🖼️</div>
                <div class="step-content">
                    <div class="step-title">Thumbnail</div>
                    <div class="step-status">Waiting...</div>
                </div>
            </div>

            <div class="upload-step" id="stepPoster">
                <div class="step-icon pending">🎬</div>
                <div class="step-content">
                    <div class="step-title">Poster</div>
                    <div class="step-status">Waiting...</div>
                </div>
            </div>
        </div>

        <div class="action-buttons" id="actionButtons" style="display: none;">
            <a href="{{ route('admin.movies') }}" class="btn btn-secondary">Back to Movies</a>
            <a href="{{ route('admin.movies.edit', $movie->id) }}" class="btn btn-primary">View Movie</a>
        </div>
    </div>
</div>

<script>
    const movieId = {{ $movie->id }};
    const statusUrl = '{{ route("admin.movies.upload-status", $movie->id) }}';
    let pollInterval;
    let isComplete = false;

    function updateProgress(status) {
        // Update progress bar
        const progressBar = document.getElementById('progressBar');
        const progressPercentage = document.getElementById('progressPercentage');
        const progressText = document.getElementById('progressText');
        
        progressBar.style.width = status.progress + '%';
        progressPercentage.textContent = status.progress + '%';
        
        // Update progress text
        if (status.is_complete) {
            progressText.textContent = 'Upload complete! 🎉';
        } else if (status.completed_steps > 0) {
            progressText.textContent = `Uploading... ${status.completed_steps} of ${status.total_steps} files completed`;
        } else {
            progressText.textContent = 'Initializing upload...';
        }

        // Update video step
        const stepVideo = document.getElementById('stepVideo');
        const stepVideoIcon = stepVideo.querySelector('.step-icon');
        const stepVideoStatus = stepVideo.querySelector('.step-status');
        
        if (status.video_uploaded) {
            stepVideo.classList.remove('in-progress');
            stepVideo.classList.add('completed');
            stepVideoIcon.className = 'step-icon completed';
            stepVideoIcon.textContent = '✓';
            stepVideoStatus.textContent = 'Uploaded successfully';
        } else {
            stepVideo.classList.add('in-progress');
            stepVideo.classList.remove('completed');
            stepVideoIcon.className = 'step-icon in-progress';
            stepVideoIcon.textContent = '⏳';
            stepVideoStatus.textContent = 'Uploading...';
        }

        // Update thumbnail step
        const stepThumbnail = document.getElementById('stepThumbnail');
        const stepThumbnailIcon = stepThumbnail.querySelector('.step-icon');
        const stepThumbnailStatus = stepThumbnail.querySelector('.step-status');
        
        if (status.thumbnail_uploaded) {
            stepThumbnail.classList.remove('in-progress');
            stepThumbnail.classList.add('completed');
            stepThumbnailIcon.className = 'step-icon completed';
            stepThumbnailIcon.textContent = '✓';
            stepThumbnailStatus.textContent = 'Uploaded successfully';
        } else if (status.video_uploaded) {
            stepThumbnail.classList.add('in-progress');
            stepThumbnail.classList.remove('completed');
            stepThumbnailIcon.className = 'step-icon in-progress';
            stepThumbnailIcon.textContent = '⏳';
            stepThumbnailStatus.textContent = 'Uploading...';
        } else {
            stepThumbnail.classList.remove('in-progress', 'completed');
            stepThumbnailIcon.className = 'step-icon pending';
            stepThumbnailIcon.textContent = '🖼️';
            stepThumbnailStatus.textContent = 'Waiting...';
        }

        // Update poster step
        const stepPoster = document.getElementById('stepPoster');
        const stepPosterIcon = stepPoster.querySelector('.step-icon');
        const stepPosterStatus = stepPoster.querySelector('.step-status');
        
        if (status.poster_uploaded) {
            stepPoster.classList.remove('in-progress');
            stepPoster.classList.add('completed');
            stepPosterIcon.className = 'step-icon completed';
            stepPosterIcon.textContent = '✓';
            stepPosterStatus.textContent = 'Uploaded successfully';
        } else if (status.thumbnail_uploaded) {
            stepPoster.classList.add('in-progress');
            stepPoster.classList.remove('completed');
            stepPosterIcon.className = 'step-icon in-progress';
            stepPosterIcon.textContent = '⏳';
            stepPosterStatus.textContent = 'Uploading...';
        } else {
            stepPoster.classList.remove('in-progress', 'completed');
            stepPosterIcon.className = 'step-icon pending';
            stepPosterIcon.textContent = '🎬';
            stepPosterStatus.textContent = 'Waiting...';
        }

        // Show success message when complete
        if (status.is_complete && !isComplete) {
            isComplete = true;
            const statusMessage = document.getElementById('statusMessage');
            statusMessage.className = 'status-message success';
            statusMessage.textContent = '✅ All files uploaded successfully! Your movie is ready.';
            statusMessage.style.display = 'block';
            
            // Show action buttons
            document.getElementById('actionButtons').style.display = 'flex';
            
            // Stop polling
            clearInterval(pollInterval);
        }
    }

    function checkStatus() {
        fetch(statusUrl)
            .then(response => response.json())
            .then(data => {
                updateProgress(data);
            })
            .catch(error => {
                console.error('Error checking status:', error);
                const statusMessage = document.getElementById('statusMessage');
                statusMessage.className = 'status-message error';
                statusMessage.textContent = 'Error checking upload status. Please refresh the page.';
                statusMessage.style.display = 'block';
            });
    }

    // Check status immediately
    checkStatus();

    // Poll every 2 seconds
    pollInterval = setInterval(checkStatus, 2000);

    // Clean up on page unload
    window.addEventListener('beforeunload', () => {
        clearInterval(pollInterval);
    });
</script>
@endsection

