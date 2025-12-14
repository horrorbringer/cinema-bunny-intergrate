@extends('layouts.app')

@section('title', 'Upload Movie - Admin')

@push('styles')
<style>
    .upload-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 40px 50px;
    }

    .page-header {
        margin-bottom: 40px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-size: 16px;
        margin-bottom: 20px;
        transition: color 0.3s;
    }

    .back-link:hover {
        color: #e50914;
    }

    .page-title {
        font-size: 42px;
        font-weight: 800;
        margin: 0;
        letter-spacing: -1px;
    }

    .upload-form {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 50px;
        backdrop-filter: blur(10px);
    }

    .form-section {
        margin-bottom: 40px;
        padding-bottom: 40px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 25px;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title::before {
        content: '';
        width: 4px;
        height: 24px;
        background: #e50914;
        border-radius: 2px;
    }

    .form-grid {
        display: grid;
        gap: 20px;
    }

    .form-grid-2 {
        grid-template-columns: repeat(2, 1fr);
    }

    .form-grid-3 {
        grid-template-columns: repeat(3, 1fr);
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group label {
        display: block;
        margin-bottom: 10px;
        font-weight: 600;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.9);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-group label .required {
        color: #e50914;
        margin-left: 4px;
    }

    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 14px 16px;
        background: rgba(255, 255, 255, 0.08);
        border: 2px solid rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        color: #ffffff;
        font-size: 16px;
        font-family: inherit;
        transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #e50914;
        background: rgba(255, 255, 255, 0.12);
        box-shadow: 0 0 0 3px rgba(229, 9, 20, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 120px;
        line-height: 1.6;
    }

    .form-help {
        display: block;
        margin-top: 8px;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.5);
    }

    /* File Upload Styles */
    .file-upload-wrapper {
        position: relative;
    }

    .file-upload-area {
        border: 2px dashed rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        background: rgba(255, 255, 255, 0.03);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-upload-area:hover {
        border-color: #e50914;
        background: rgba(229, 9, 20, 0.05);
    }

    .file-upload-area.dragover {
        border-color: #e50914;
        background: rgba(229, 9, 20, 0.1);
        transform: scale(1.02);
    }

    .file-upload-icon {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.6;
    }

    .file-upload-text {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #ffffff;
    }

    .file-upload-hint {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.5);
        margin-bottom: 15px;
    }

    .file-upload-btn {
        display: inline-block;
        padding: 12px 24px;
        background: #e50914;
        color: #ffffff;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .file-upload-btn:hover {
        background: #f40612;
        transform: translateY(-2px);
    }

    .file-input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .file-preview {
        margin-top: 20px;
        padding: 20px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        display: none;
    }

    .file-preview.active {
        display: block;
    }

    .file-preview-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .file-preview-item:last-child {
        margin-bottom: 0;
    }

    .file-preview-icon {
        font-size: 32px;
        opacity: 0.7;
    }

    .file-preview-info {
        flex: 1;
    }

    .file-preview-name {
        font-weight: 600;
        margin-bottom: 4px;
    }

    .file-preview-size {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.5);
    }

    .file-remove {
        color: #e50914;
        cursor: pointer;
        font-size: 20px;
        padding: 5px;
        transition: transform 0.3s;
    }

    .file-remove:hover {
        transform: scale(1.2);
    }

    /* Genre Checkboxes */
    .genre-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 12px;
        margin-top: 15px;
    }

    .genre-checkbox {
        position: relative;
    }

    .genre-checkbox input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .genre-checkbox label {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.05);
        border: 2px solid rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        text-transform: none;
        letter-spacing: 0;
        margin: 0;
    }

    .genre-checkbox input[type="checkbox"]:checked + label {
        background: rgba(229, 9, 20, 0.2);
        border-color: #e50914;
        color: #ffffff;
    }

    .genre-checkbox label::before {
        content: '';
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 4px;
        transition: all 0.3s;
    }

    .genre-checkbox input[type="checkbox"]:checked + label::before {
        background: #e50914;
        border-color: #e50914;
        content: '✓';
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 14px;
        font-weight: bold;
    }

    /* Toggle Switches */
    .toggle-group {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .toggle-switch {
        position: relative;
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: rgba(255, 255, 255, 0.05);
        border: 2px solid rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .toggle-switch:hover {
        border-color: rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.08);
    }

    .toggle-switch.active {
        background: rgba(229, 9, 20, 0.15);
        border-color: #e50914;
    }

    .toggle-switch input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-switch-label {
        flex: 1;
        font-weight: 600;
        margin: 0;
        text-transform: none;
        letter-spacing: 0;
    }

    .toggle-switch-slider {
        position: relative;
        width: 50px;
        height: 28px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 14px;
        transition: all 0.3s;
    }

    .toggle-switch-slider::before {
        content: '';
        position: absolute;
        width: 22px;
        height: 22px;
        background: #ffffff;
        border-radius: 50%;
        top: 3px;
        left: 3px;
        transition: all 0.3s;
    }

    .toggle-switch input:checked + .toggle-switch-label + .toggle-switch-slider {
        background: #e50914;
    }

    .toggle-switch input:checked + .toggle-switch-label + .toggle-switch-slider::before {
        transform: translateX(22px);
    }

    /* Upload Progress */
    .upload-progress {
        display: none;
        margin-bottom: 30px;
        padding: 25px;
        background: rgba(255, 255, 255, 0.05);
        border: 2px solid rgba(229, 9, 20, 0.3);
        border-radius: 12px;
    }

    .upload-progress.active {
        display: block;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .progress-text {
        font-weight: 600;
        font-size: 16px;
    }

    .progress-percent {
        font-weight: 700;
        color: #e50914;
        font-size: 18px;
    }

    .progress-bar-container {
        background: rgba(255, 255, 255, 0.1);
        height: 12px;
        border-radius: 6px;
        overflow: hidden;
    }

    .progress-bar {
        background: linear-gradient(90deg, #e50914, #f40612);
        height: 100%;
        width: 0%;
        transition: width 0.3s ease;
        border-radius: 6px;
        position: relative;
        overflow: hidden;
    }

    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 1.5s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    /* Submit Button */
    .submit-section {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .submit-btn {
        width: 100%;
        padding: 18px;
        background: #e50914;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .submit-btn:hover:not(:disabled) {
        background: #f40612;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(229, 9, 20, 0.4);
    }

    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .submit-hint {
        text-align: center;
        margin-top: 20px;
        padding: 15px;
        background: rgba(229, 9, 20, 0.1);
        border-radius: 8px;
        color: rgba(255, 255, 255, 0.8);
        font-size: 14px;
        line-height: 1.6;
    }

    .submit-hint-icon {
        display: inline-block;
        margin-right: 8px;
        font-size: 18px;
    }

    /* Error Messages */
    .alert-error {
        background: rgba(220, 53, 69, 0.2);
        border: 2px solid rgba(220, 53, 69, 0.5);
        color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .alert-error ul {
        margin: 0;
        padding-left: 20px;
    }

    .alert-error li {
        margin-bottom: 8px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .upload-container {
            padding: 20px;
        }

        .upload-form {
            padding: 30px 20px;
        }

        .form-grid-2,
        .form-grid-3 {
            grid-template-columns: 1fr;
        }

        .toggle-group {
            grid-template-columns: 1fr;
        }

        .page-title {
            font-size: 28px;
        }
    }
</style>
@endpush

@section('content')
<div class="upload-container">
    <div class="page-header">
        <a href="{{ route('admin.movies') }}" class="back-link">
            <span>←</span> Back to Movies
        </a>
        <h1 class="page-title">Upload New Movie</h1>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.movies.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm" class="upload-form">
        @csrf
        
        <!-- Basic Information -->
        <div class="form-section">
            <h2 class="section-title">Basic Information</h2>
            
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label for="title">Movie Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Enter movie title">
                </div>

                <div class="form-group">
                    <label for="year">Release Year</label>
                    <input type="number" id="year" name="year" value="{{ old('year') }}" min="1900" max="{{ date('Y') }}" placeholder="e.g. 2024">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5" placeholder="Enter movie description...">{{ old('description') }}</textarea>
                <span class="form-help">Provide a brief synopsis of the movie</span>
            </div>
        </div>

        <!-- Movie Details -->
        <div class="form-section">
            <h2 class="section-title">Movie Details</h2>
            
            <div class="form-grid form-grid-3">
                <div class="form-group">
                    <label for="duration">Duration (minutes)</label>
                    <input type="number" id="duration" name="duration" value="{{ old('duration') }}" min="1" placeholder="e.g. 120">
                </div>

                <div class="form-group">
                    <label for="rating">Rating (0-10)</label>
                    <input type="number" id="rating" name="rating" value="{{ old('rating') }}" min="0" max="10" step="0.1" placeholder="e.g. 8.5">
                </div>

                <div class="form-group">
                    <label for="age_rating">Age Rating</label>
                    <select id="age_rating" name="age_rating">
                        <option value="">Select age rating...</option>
                        <option value="G" {{ old('age_rating') == 'G' ? 'selected' : '' }}>G - General Audiences</option>
                        <option value="PG" {{ old('age_rating') == 'PG' ? 'selected' : '' }}>PG - Parental Guidance</option>
                        <option value="PG-13" {{ old('age_rating') == 'PG-13' ? 'selected' : '' }}>PG-13 - Parents Strongly Cautioned</option>
                        <option value="R" {{ old('age_rating') == 'R' ? 'selected' : '' }}>R - Restricted</option>
                        <option value="NC-17" {{ old('age_rating') == 'NC-17' ? 'selected' : '' }}>NC-17 - Adults Only</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="genres">Genres</label>
                <div class="genre-grid">
                    @foreach($genres as $genre)
                        <div class="genre-checkbox">
                            <input type="checkbox" id="genre_{{ $genre->id }}" name="genres[]" value="{{ $genre->id }}" {{ in_array($genre->id, old('genres', [])) ? 'checked' : '' }}>
                            <label for="genre_{{ $genre->id }}">{{ $genre->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Video File -->
        <div class="form-section">
            <h2 class="section-title">Video File</h2>
            
            <div class="form-group">
                <label for="file">Video File <span class="required">*</span></label>
                <div class="file-upload-wrapper">
                    <div class="file-upload-area" id="videoUploadArea">
                        <div class="file-upload-icon">🎬</div>
                        <div class="file-upload-text">Click to upload or drag and drop</div>
                        <div class="file-upload-hint">Max file size: 5GB • MP4, MOV, AVI, MKV, WEBM</div>
                        <div class="file-upload-btn">Choose Video File</div>
                        <input type="file" id="file" name="file" accept="video/*" required class="file-input">
                    </div>
                    <div class="file-preview" id="videoPreview"></div>
                </div>
            </div>
        </div>

        <!-- Images -->
        <div class="form-section">
            <h2 class="section-title">Images</h2>
            
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label for="thumbnail">Thumbnail Image</label>
                    <div class="file-upload-wrapper">
                        <div class="file-upload-area" id="thumbnailUploadArea" style="padding: 30px;">
                            <div class="file-upload-icon">🖼️</div>
                            <div class="file-upload-text">Thumbnail</div>
                            <div class="file-upload-hint">Recommended: 300x450px</div>
                            <div class="file-upload-btn">Choose Image</div>
                            <input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="file-input">
                        </div>
                        <div class="file-preview" id="thumbnailPreview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="poster">Poster Image</label>
                    <div class="file-upload-wrapper">
                        <div class="file-upload-area" id="posterUploadArea" style="padding: 30px;">
                            <div class="file-upload-icon">🎨</div>
                            <div class="file-upload-text">Poster</div>
                            <div class="file-upload-hint">Recommended: 1920x1080px</div>
                            <div class="file-upload-btn">Choose Image</div>
                            <input type="file" id="poster" name="poster" accept="image/*" class="file-input">
                        </div>
                        <div class="file-preview" id="posterPreview"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured & Trending -->
        <div class="form-section">
            <h2 class="section-title">Visibility</h2>
            
            <div class="toggle-group">
                <div class="toggle-switch {{ old('is_featured') ? 'active' : '' }}" onclick="toggleSwitch('is_featured')">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                    <label for="is_featured" class="toggle-switch-label">Featured Movie</label>
                    <div class="toggle-switch-slider"></div>
                </div>

                <div class="toggle-switch {{ old('is_trending') ? 'active' : '' }}" onclick="toggleSwitch('is_trending')">
                    <input type="checkbox" id="is_trending" name="is_trending" value="1" {{ old('is_trending') ? 'checked' : '' }}>
                    <label for="is_trending" class="toggle-switch-label">Trending Movie</label>
                    <div class="toggle-switch-slider"></div>
                </div>
            </div>
        </div>

        <!-- Upload Progress -->
        <div id="uploadProgress" class="upload-progress">
            <div class="progress-header">
                <span class="progress-text">Uploading to server...</span>
                <span class="progress-percent" id="progressPercent">0%</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar" id="progressBar"></div>
            </div>
        </div>

        <!-- Submit -->
        <div class="submit-section">
            <button type="submit" class="submit-btn" id="submitBtn">
                <span id="submitText">Save & Queue Upload</span>
            </button>
            <div class="submit-hint">
                <span class="submit-hint-icon">⚡</span>
                Files are saved locally first, then uploaded to Bunny.net in the background. You'll get immediate response!
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // File upload handlers
    function setupFileUpload(areaId, inputId, previewId) {
        const area = document.getElementById(areaId);
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const btn = area.querySelector('.file-upload-btn');

        // Click to upload
        area.addEventListener('click', () => input.click());
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            input.click();
        });

        // Drag and drop
        area.addEventListener('dragover', (e) => {
            e.preventDefault();
            area.classList.add('dragover');
        });

        area.addEventListener('dragleave', () => {
            area.classList.remove('dragover');
        });

        area.addEventListener('drop', (e) => {
            e.preventDefault();
            area.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                input.files = e.dataTransfer.files;
                showFilePreview(input, preview);
            }
        });

        // File selected
        input.addEventListener('change', () => {
            showFilePreview(input, preview);
        });
    }

    function showFilePreview(input, preview) {
        if (input.files.length > 0) {
            const file = input.files[0];
            const fileSize = formatFileSize(file.size);
            const fileName = file.name;
            const fileType = file.type.includes('video') ? '🎬' : file.type.includes('image') ? '🖼️' : '📄';

            preview.innerHTML = `
                <div class="file-preview-item">
                    <div class="file-preview-icon">${fileType}</div>
                    <div class="file-preview-info">
                        <div class="file-preview-name">${fileName}</div>
                        <div class="file-preview-size">${fileSize}</div>
                    </div>
                    <div class="file-remove" onclick="removeFile('${input.id}', '${preview.id}')">×</div>
                </div>
            `;
            preview.classList.add('active');
        }
    }

    function removeFile(inputId, previewId) {
        document.getElementById(inputId).value = '';
        document.getElementById(previewId).classList.remove('active');
        document.getElementById(previewId).innerHTML = '';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    function toggleSwitch(id) {
        const checkbox = document.getElementById(id);
        const toggle = checkbox.closest('.toggle-switch');
        checkbox.checked = !checkbox.checked;
        toggle.classList.toggle('active', checkbox.checked);
    }

    // Initialize file uploads
    setupFileUpload('videoUploadArea', 'file', 'videoPreview');
    setupFileUpload('thumbnailUploadArea', 'thumbnail', 'thumbnailPreview');
    setupFileUpload('posterUploadArea', 'poster', 'posterPreview');

    // Form submission
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        const fileInput = document.getElementById('file');
        const file = fileInput.files[0];
        
        if (file && file.size > 0) {
            const progressDiv = document.getElementById('uploadProgress');
            const progressBar = document.getElementById('progressBar');
            const progressPercent = document.getElementById('progressPercent');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            
            progressDiv.classList.add('active');
            submitBtn.disabled = true;
            submitText.textContent = 'Uploading...';
            
            // Simulate progress (actual progress would need XMLHttpRequest)
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 12;
                if (progress > 95) progress = 95;
                progressBar.style.width = progress + '%';
                progressPercent.textContent = Math.round(progress) + '%';
            }, 400);
            
            // Clear interval when form submits
            setTimeout(() => clearInterval(interval), 8000);
        }
    });
</script>
@endpush
@endsection
