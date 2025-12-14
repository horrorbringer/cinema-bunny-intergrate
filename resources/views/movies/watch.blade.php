@extends('layouts.app')

@section('title', 'Watch ' . $movie->title . ' - Cinema Bunny')

@push('styles')
<style>
    .video-container {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    .video-player-wrapper {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        background: #000;
        border-radius: 8px;
        overflow: hidden;
    }

    .video-player-wrapper video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .play-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.3);
        cursor: pointer;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        z-index: 10;
    }

    .play-overlay.hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    .play-button {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(229, 9, 20, 0.9);
        border: 4px solid #ffffff;
        color: #ffffff;
        font-size: 40px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    }

    .play-button:hover {
        background: rgba(244, 6, 18, 1);
        transform: scale(1.1);
        box-shadow: 0 6px 30px rgba(0, 0, 0, 0.7);
    }

    .play-icon {
        margin-left: 5px; /* Adjust play icon position */
    }

    .video-info {
        padding: 30px 50px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .video-title {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: #ffffff;
        text-decoration: none;
        font-size: 18px;
    }

    .back-link:hover {
        color: #e50914;
    }

    /* Quality Indicator */
    .quality-info {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(0, 0, 0, 0.7);
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #ffffff;
        z-index: 5;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: none;
    }

    .quality-info.active {
        display: block;
    }

    .quality-badge {
        display: inline-block;
        background: #e50914;
        color: #ffffff;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        margin-left: 8px;
    }

    /* Quality Selector */
    .quality-selector {
        position: absolute;
        bottom: 60px;
        right: 20px;
        z-index: 100;
    }

    .quality-btn {
        background: rgba(0, 0, 0, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
        padding: 10px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        transition: all 0.2s;
        backdrop-filter: blur(10px);
    }

    .quality-btn:hover {
        background: rgba(229, 9, 20, 0.8);
        border-color: #e50914;
    }

    .quality-menu {
        position: absolute;
        bottom: 100%;
        right: 0;
        margin-bottom: 10px;
        background: rgba(0, 0, 0, 0.95);
        border-radius: 8px;
        padding: 8px 0;
        min-width: 180px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .quality-option {
        width: 100%;
        padding: 12px 20px;
        background: transparent;
        border: none;
        color: #ffffff;
        text-align: left;
        cursor: pointer;
        transition: background 0.2s;
        font-size: 14px;
        font-weight: 500;
    }

    .quality-option:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .quality-option.active {
        background: rgba(229, 9, 20, 0.3);
        color: #e50914;
        font-weight: 700;
    }

    /* Video Stats */
    .video-stats {
        display: flex;
        gap: 20px;
        margin-top: 20px;
        padding: 20px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        flex-wrap: wrap;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .stat-label {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 18px;
        font-weight: 700;
        color: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="video-container">
    <a href="{{ route('movies.show', $movie->slug) }}" class="back-link">← Back to {{ $movie->title }}</a>
    <div class="video-player-wrapper">
        <video id="videoPlayer" controls preload="metadata">
            <source src="{{ $url }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="quality-info" id="qualityInfo">
            <span id="qualityText">Loading...</span>
        </div>
        @if(isset($qualityUrls) && count($qualityUrls) > 1)
        <div class="quality-selector" id="qualitySelector">
            <button class="quality-btn" id="qualityBtn">
                <span id="currentQuality">Quality</span>
                <span style="margin-left: 5px;">▼</span>
            </button>
            <div class="quality-menu" id="qualityMenu" style="display: none;">
                @foreach($qualityUrls as $quality => $qualityUrl)
                    <button class="quality-option" data-quality="{{ $quality }}" data-url="{{ $qualityUrl }}">
                        {{ $quality === 'auto' ? 'Auto (1080p)' : strtoupper($quality) }}
                    </button>
                @endforeach
            </div>
        </div>
        @endif
        <div class="play-overlay" id="playOverlay">
            <button class="play-button" id="playButton">
                <span class="play-icon">▶</span>
            </button>
        </div>
    </div>
</div>

<div class="video-info">
    <h1 class="video-title">{{ $movie->title }}</h1>
    <p>{{ $movie->description ?? '' }}</p>
    
    <div class="video-stats" id="videoStats">
        <div class="stat-item">
            <span class="stat-label">Resolution</span>
            <span class="stat-value" id="statResolution">-</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Quality</span>
            <span class="stat-value" id="statQuality">-</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Bitrate</span>
            <span class="stat-value" id="statBitrate">-</span>
        </div>
        @if($movie->duration)
        <div class="stat-item">
            <span class="stat-label">Duration</span>
            <span class="stat-value">{{ gmdate('H:i:s', $movie->duration * 60) }}</span>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    const video = document.getElementById('videoPlayer');
    const playOverlay = document.getElementById('playOverlay');
    const playButton = document.getElementById('playButton');
    const slug = '{{ $movie->slug }}';
    let progressInterval;
    let userInteracted = false;
    
    // Quality selector
    @if(isset($qualityUrls) && count($qualityUrls) > 1)
    const qualityUrls = @json($qualityUrls);
    const qualityBtn = document.getElementById('qualityBtn');
    const qualityMenu = document.getElementById('qualityMenu');
    const currentQualitySpan = document.getElementById('currentQuality');
    let currentQuality = '{{ array_key_first($qualityUrls) }}';
    
    // Update current quality display
    function updateQualityDisplay() {
        const qualityLabel = currentQuality === 'auto' ? 'Auto (1080p)' : currentQuality.toUpperCase();
        currentQualitySpan.textContent = qualityLabel;
        
        // Update active state in menu
        document.querySelectorAll('.quality-option').forEach(option => {
            if (option.dataset.quality === currentQuality) {
                option.classList.add('active');
            } else {
                option.classList.remove('active');
            }
        });
    }
    
    // Switch quality
    function switchQuality(quality, url) {
        const wasPlaying = !video.paused;
        const currentTime = video.currentTime;
        
        // Update video source
        video.src = url;
        video.load();
        
        // Restore playback position
        video.addEventListener('loadedmetadata', function() {
            video.currentTime = currentTime;
            if (wasPlaying) {
                video.play();
            }
        }, { once: true });
        
        currentQuality = quality;
        updateQualityDisplay();
        qualityMenu.style.display = 'none';
    }
    
    // Quality button click
    qualityBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        qualityMenu.style.display = qualityMenu.style.display === 'none' ? 'block' : 'none';
    });
    
    // Quality option clicks
    document.querySelectorAll('.quality-option').forEach(option => {
        option.addEventListener('click', function() {
            const quality = this.dataset.quality;
            const url = this.dataset.url;
            switchQuality(quality, url);
        });
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!qualityBtn.contains(e.target) && !qualityMenu.contains(e.target)) {
            qualityMenu.style.display = 'none';
        }
    });
    
    // Initialize quality display
    updateQualityDisplay();
    @endif

    // Ensure video has audio enabled
    video.volume = 1.0; // Set volume to 100%
    video.muted = false; // Ensure not muted

    // Play button click handler
    function playVideo() {
        video.play().then(() => {
            playOverlay.classList.add('hidden');
            userInteracted = true;
            // Ensure audio is unmuted after user interaction
            video.muted = false;
            video.volume = 1.0;
        }).catch(error => {
            console.error('Play failed:', error);
            alert('Unable to play video. Please click the play button in the video controls.');
        });
    }

    playButton.addEventListener('click', playVideo);
    playOverlay.addEventListener('click', playVideo);

    // Hide overlay when video starts playing
    video.addEventListener('play', function() {
        playOverlay.classList.add('hidden');
        userInteracted = true;
        // Ensure audio is enabled
        video.muted = false;
        video.volume = 1.0;
    });

    // Show overlay when video is paused
    video.addEventListener('pause', function() {
        if (!video.ended) {
            playOverlay.classList.remove('hidden');
        }
        if (progressInterval) {
            clearInterval(progressInterval);
        }
    });

    // Show overlay when video ends
    video.addEventListener('ended', function() {
        playOverlay.classList.remove('hidden');
        if (progressInterval) {
            clearInterval(progressInterval);
        }
    });

    // Video quality detection and display
    function detectVideoQuality() {
        const videoWidth = video.videoWidth;
        const videoHeight = video.videoHeight;
        const qualityInfo = document.getElementById('qualityInfo');
        const qualityText = document.getElementById('qualityText');
        const statResolution = document.getElementById('statResolution');
        const statQuality = document.getElementById('statQuality');
        const statBitrate = document.getElementById('statBitrate');

        if (videoWidth && videoHeight) {
            // Determine quality label
            let qualityLabel = 'SD';
            let qualityBadge = '';
            
            if (videoHeight >= 2160) {
                qualityLabel = '4K';
                qualityBadge = '<span class="quality-badge">4K</span>';
            } else if (videoHeight >= 1440) {
                qualityLabel = '1440p';
                qualityBadge = '<span class="quality-badge">QHD</span>';
            } else if (videoHeight >= 1080) {
                qualityLabel = '1080p';
                qualityBadge = '<span class="quality-badge">FHD</span>';
            } else if (videoHeight >= 720) {
                qualityLabel = '720p';
                qualityBadge = '<span class="quality-badge">HD</span>';
            } else if (videoHeight >= 480) {
                qualityLabel = '480p';
                qualityBadge = '<span class="quality-badge">SD</span>';
            } else {
                qualityLabel = '360p';
                qualityBadge = '<span class="quality-badge">SD</span>';
            }

            // Update quality info
            qualityText.innerHTML = `${videoWidth}×${videoHeight} ${qualityBadge}`;
            qualityInfo.classList.add('active');

            // Update stats
            statResolution.textContent = `${videoWidth}×${videoHeight}`;
            statQuality.textContent = qualityLabel;

            // Estimate bitrate (if available)
            if (video.buffered.length > 0) {
                const bufferedEnd = video.buffered.end(video.buffered.length - 1);
                const bufferedStart = video.buffered.start(0);
                const duration = bufferedEnd - bufferedStart;
                
                // This is an estimate - actual bitrate detection requires more complex calculation
                if (duration > 0) {
                    // We can't directly get bitrate from HTML5 video, so we'll show resolution-based estimate
                    const estimatedBitrate = estimateBitrate(videoWidth, videoHeight);
                    statBitrate.textContent = estimatedBitrate;
                }
            }
        }
    }

    function estimateBitrate(width, height) {
        // Rough bitrate estimates based on resolution
        const pixels = width * height;
        if (pixels >= 8294400) return '15-25 Mbps'; // 4K
        if (pixels >= 3686400) return '8-12 Mbps';  // 1440p
        if (pixels >= 2073600) return '4-8 Mbps';   // 1080p
        if (pixels >= 921600) return '2-4 Mbps';     // 720p
        if (pixels >= 414720) return '1-2 Mbps';      // 480p
        return '0.5-1 Mbps';                          // 360p
    }

    // Ensure volume controls are visible and audio is enabled
    video.addEventListener('loadedmetadata', function() {
        video.volume = 1.0;
        video.muted = false;
        detectVideoQuality();
    });

    // Update quality info when video dimensions are available
    video.addEventListener('loadeddata', function() {
        detectVideoQuality();
    });

    // Update quality on resize (if video changes)
    video.addEventListener('resize', function() {
        detectVideoQuality();
    });

    // Save progress every 10 seconds (only for authenticated users)
    @auth
    video.addEventListener('play', function() {
        progressInterval = setInterval(function() {
            if (!video.paused && !video.ended) {
                fetch(`/watch/${slug}/progress`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        progress: Math.floor(video.currentTime)
                    })
                });
            }
        }, 10000);
    });

    // Save progress on page unload
    window.addEventListener('beforeunload', function() {
        if (!video.paused) {
            navigator.sendBeacon(`/watch/${slug}/progress`, JSON.stringify({
                progress: Math.floor(video.currentTime),
                _token: document.querySelector('meta[name="csrf-token"]').content
            }));
        }
    });
    @endauth

    // Keyboard controls
    document.addEventListener('keydown', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        
        switch(e.code) {
            case 'Space':
                e.preventDefault();
                if (video.paused) {
                    video.play();
                } else {
                    video.pause();
                }
                break;
            case 'ArrowLeft':
                e.preventDefault();
                video.currentTime -= 10;
                break;
            case 'ArrowRight':
                e.preventDefault();
                video.currentTime += 10;
                break;
            case 'ArrowUp':
                e.preventDefault();
                video.volume = Math.min(1, video.volume + 0.1);
                break;
            case 'ArrowDown':
                e.preventDefault();
                video.volume = Math.max(0, video.volume - 0.1);
                break;
            case 'KeyM':
                e.preventDefault();
                video.muted = !video.muted;
                break;
        }
    });
</script>
@endpush
@endsection

