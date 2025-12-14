<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\AdminController;

// Home page
Route::get('/', [MovieController::class, 'index'])->name('movies.index');

// Movie routes
Route::get('/movie/{slug}', [MovieController::class, 'show'])->name('movies.show');
Route::get('/watch/{slug}', [MovieController::class, 'watch'])->name('movies.watch');
Route::post('/watch/{slug}/progress', [MovieController::class, 'updateProgress'])->name('movies.progress');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Favorites routes
Route::post('/favorite/{slug}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/movies', [AdminController::class, 'movies'])->name('movies');
    Route::get('/movies/create', [AdminController::class, 'create'])->name('movies.create');
    Route::post('/movies', [AdminController::class, 'store'])->name('movies.store');
    Route::get('/movies/{id}/edit', [AdminController::class, 'edit'])->name('movies.edit');
    Route::match(['put', 'patch'], '/movies/{id}', [AdminController::class, 'update'])->name('movies.update');
    Route::post('/movies/{id}/add-quality', [AdminController::class, 'addQuality'])->name('movies.add-quality');
    Route::delete('/movies/{id}/quality/{quality}', [AdminController::class, 'removeQuality'])->name('movies.remove-quality');
    Route::delete('/movies/{id}', [AdminController::class, 'destroy'])->name('movies.destroy');
});

// Legacy upload route (redirects to admin)
Route::get('/video/upload', function () {
    if (auth()->check() && auth()->user()->is_admin) {
        return redirect()->route('admin.movies.create');
    }
    return redirect()->route('login')->with('error', 'Admin access required');
})->middleware('auth');
