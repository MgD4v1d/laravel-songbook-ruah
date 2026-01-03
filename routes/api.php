<?php

use App\Http\Controllers\Api\SongController;
use Illuminate\Support\Facades\Route;


Route::middleware('api.key')->group(function(){
    Route::get('/songs/metadata', [SongController::class, 'metadata']);
    Route::get('/songs/recent', [SongController::class, 'recent']);
    Route::get('/songs/last-modified', [SongController::class, 'lastModified']);
    Route::post('/songs/batch', [SongController::class, 'batch']);
    Route::get('/songs/stats', [SongController::class, 'stats']);
    Route::get('/songs/{song}', [SongController::class, 'show']);
});



Route::get('/health', [SongController::class, 'health']);
