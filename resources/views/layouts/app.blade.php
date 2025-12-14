<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cinema Bunny - Stream Movies & TV Shows')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #141414;
            color: #ffffff;
            line-height: 1.6;
        }

        .navbar {
            background: linear-gradient(180deg, rgba(0,0,0,0.7) 10%, transparent);
            padding: 20px 50px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.3s;
        }

        .navbar.scrolled {
            background: #141414;
        }

        .navbar-brand {
            font-size: 28px;
            font-weight: bold;
            color: #e50914;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #e50914;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-box input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #ffffff;
            padding: 8px 15px;
            border-radius: 4px;
            width: 300px;
        }

        .search-box input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #e50914;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #f40612;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 50px;
        }

        .main-content {
            margin-top: 80px;
        }

        .hero-section {
            height: 80vh;
            position: relative;
            background: linear-gradient(to bottom, rgba(0,0,0,0.4), #141414);
            display: flex;
            align-items: center;
            padding: 0 50px;
            margin-bottom: 50px;
        }

        .hero-content {
            max-width: 600px;
            z-index: 2;
        }

        .hero-title {
            font-size: 64px;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
        }

        .hero-description {
            font-size: 20px;
            margin-bottom: 30px;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.9);
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
        }

        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
            padding-left: 50px;
        }

        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 0 50px;
            margin-bottom: 50px;
        }

        .movie-card {
            position: relative;
            cursor: pointer;
            transition: transform 0.3s;
            border-radius: 8px;
            overflow: hidden;
        }

        .movie-card:hover {
            transform: scale(1.05);
            z-index: 10;
        }

        .movie-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }

        .movie-card-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
            padding: 20px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .movie-card:hover .movie-card-overlay {
            opacity: 1;
        }

        .movie-card-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .movie-card-info {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }

        .movie-row {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding: 20px 50px;
            scroll-behavior: smooth;
        }

        .movie-row::-webkit-scrollbar {
            height: 8px;
        }

        .movie-row::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .movie-row::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }

        .movie-item {
            min-width: 200px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .movie-item:hover {
            transform: scale(1.1);
        }

        .movie-item img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #28a745;
            color: #ffffff;
        }

        .alert-error {
            background: #dc3545;
            color: #ffffff;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            color: #ffffff;
            font-size: 16px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #e50914;
        }

        .rating {
            color: #f5c518;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }

            .search-box input {
                width: 150px;
            }

            .hero-title {
                font-size: 36px;
            }

            .movie-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 15px;
                padding: 0 20px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar" id="navbar">
        <a href="{{ route('movies.index') }}" class="navbar-brand">Cinema Bunny</a>
        <div class="nav-links">
            <a href="{{ route('movies.index') }}">Home</a>
            @auth
                <a href="{{ route('favorites.index') }}">My List</a>
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" style="color: #e50914; font-weight: bold;">Admin</a>
                @endif
            @endauth
            <form action="{{ route('movies.index') }}" method="GET" class="search-box">
                <input type="text" name="search" placeholder="Search movies..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-secondary" style="padding: 8px 15px;">Search</button>
            </form>
            @auth
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="background: transparent; border: none; padding: 0;">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Sign Up</a>
            @endauth
        </div>
    </nav>

    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

