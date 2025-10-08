<?php

use App\Http\Controllers\Api\SongController;
use Illuminate\Support\Facades\Route;


Route::get('/songs/metadata', [SongController::class, 'metadata']);
Route::get('/songs/last-modified', [SongController::class, 'lastModified']);
Route::post('/songs/batch', [SongController::class, 'batch']);
Route::get('/health', [SongController::class, 'health']);

Route::get('/songs/{song}', [SongController::class, 'show']);