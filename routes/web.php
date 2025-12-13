<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoTestController;
use App\Jobs\TestCinemaQueueJob;
use Illuminate\Support\Facades\Storage;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/video/upload', function () {
    return view('upload');
});

Route::post('/video/upload', [VideoTestController::class, 'upload'])->name('video.upload');

Route::get('/video/watch/{id}', [VideoTestController::class, 'watch'])->name('video.watch');

Route::get('/test-queue', function () {
    TestCinemaQueueJob::dispatch();

    return 'Cinema job dispatched!';
});
Route::get('/bunny-test', function () {
    // 1. Try to write a very small test file
    Storage::disk('bunny')->put('test-laravel.txt', 'Hello from Laravel + Bunny');

    // 2. List files in the root
    $files = Storage::disk('bunny')->files('/');

    return [
        'disk'  => config('filesystems.disks.bunny'),
        'files' => $files,
    ];
});
