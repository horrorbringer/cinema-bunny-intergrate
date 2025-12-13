<!DOCTYPE html>
<html>
<head>
    <title>Watch {{ $movie->title }}</title>
</head>
<body>
<h1>{{ $movie->title }}</h1>
<video width="720" height="400" controls>
    <source src="{{ $url }}" type="video/mp4">
    Your browser does not support the video tag.
</video>
</body>
</html>
