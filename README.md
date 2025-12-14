# Cinema Bunny - Netflix-like Video Streaming Platform

A modern video streaming platform built with Laravel Blade and Bunny.net CDN, inspired by Netflix.

## Features

- 🎬 **Movie Streaming**: Stream videos directly from Bunny.net CDN
- 🔍 **Search & Browse**: Search movies and browse by genres
- 👤 **User Authentication**: Register, login, and manage accounts
- ⭐ **Favorites/My List**: Save movies to watch later
- 📊 **Watch History**: Track your viewing progress
- 🎨 **Modern UI**: Netflix-inspired dark theme interface
- 📱 **Responsive Design**: Works on desktop, tablet, and mobile

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js and NPM
- MySQL/PostgreSQL/SQLite
- Bunny.net account with Storage Zone configured

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd cinema-bunny-intergrate
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Set up Bunny.net credentials in `.env`**
   ```env
   BUNNY_STORAGE_HOST=your-storage-host.storage.bunnycdn.com
   BUNNY_STORAGE_USERNAME=your-storage-username
   BUNNY_STORAGE_PASSWORD=your-storage-password
   BUNNY_STORAGE_ROOT=/
   BUNNY_CDN_DOMAIN=your-cdn-domain.b-cdn.net
   BUNNY_API_KEY=your-api-key
   ```

6. **Configure database in `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cinema_bunny
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

8. **Build assets**
   ```bash
   npm run build
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

   Or use the dev script:
   ```bash
   composer run dev
   ```

## Usage

### Uploading Movies

1. Register/Login to your account
2. Navigate to `/video/upload`
3. Fill in movie details:
   - Title (required)
   - Description
   - Year, Duration, Rating
   - Upload video file
   - Optionally upload thumbnail and poster images
   - Mark as Featured or Trending
4. Click "Upload Movie"

### Watching Movies

1. Browse movies on the home page
2. Click on any movie to view details
3. Click "Play" to start streaming
4. Your watch progress is automatically saved

### Managing Favorites

1. Click "+ My List" on any movie page
2. View all favorites in "My List" from the navigation

## Project Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── MovieController.php      # Movie browsing, search, watch
│       ├── AuthController.php       # Authentication
│       ├── FavoriteController.php    # Favorites management
│       └── VideoTestController.php  # Video upload
├── Models/
│   ├── Movie.php                    # Movie model
│   ├── Genre.php                    # Genre model
│   ├── WatchHistory.php             # Watch history model
│   └── Favorite.php                 # Favorite model
resources/
└── views/
    ├── layouts/
    │   └── app.blade.php            # Main layout
    ├── movies/
    │   ├── index.blade.php          # Home/browse page
    │   ├── show.blade.php           # Movie details
    │   └── watch.blade.php          # Video player
    ├── auth/
    │   ├── login.blade.php          # Login page
    │   └── register.blade.php      # Registration page
    └── favorites/
        └── index.blade.php         # My List page
```

## Database Schema

- **movies**: Stores movie information
- **genres**: Movie genres/categories
- **movie_genre**: Many-to-many relationship
- **watch_history**: Tracks user viewing progress
- **favorites**: User's saved movies

## Bunny.net Setup

1. Create a Storage Zone in Bunny.net
2. Get your Storage Zone credentials:
   - Host: `[zone].storage.bunnycdn.com`
   - Username: Your storage zone name
   - Password: Your storage zone password
3. Create a Pull Zone (optional, for CDN)
4. Configure domain in `.env`

## Features in Detail

### Search
- Search movies by title or description
- Filter by genre
- Real-time search results

### Watch History
- Automatically tracks viewing progress
- Resume from where you left off
- Progress saved every 10 seconds

### Responsive Design
- Mobile-friendly interface
- Netflix-inspired dark theme
- Smooth animations and transitions

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues and questions, please open an issue on GitHub.
