<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoTestController;
use App\Jobs\TestCinemaQueueJob;


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
