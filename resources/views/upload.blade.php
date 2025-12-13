<!DOCTYPE html>
<html>
<head>
    <title>Video Upload Test</title>
</head>
<body>
@if(session('success'))
    <p>{{ session('success') }}</p>
@endif

<form action="{{ route('video.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label>Movie Title:</label>
    <input type="text" name="title" required>
    <br>
    <label>Choose Video:</label>
    <input type="file" name="file" required>
    <br>
    <button type="submit">Upload</button>
</form>
</body>
</html>
