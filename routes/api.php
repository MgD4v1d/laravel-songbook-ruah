<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SongController;
use App\Http\Controllers\Api\StatsController;
use Illuminate\Support\Facades\Route;


// Ruta pública de login
Route::post('/auth/login', [AuthController::class, 'login']);



Route::middleware('api.key')->group(function(){

    Route::prefix('categories')->group(function(){
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/last-modified', [CategoryController::class, 'lastModified']);
        Route::get('/stats', [CategoryController::class, 'stats']);
        Route::get('/{slug}', [CategoryController::class, 'show']);
    });

    Route::prefix('songs')->group(function(){
        Route::get('/metadata', [SongController::class, 'metadata']);
        Route::get('/metadata/category/{categorySlug}', [SongController::class, 'metadataByCategory']);
        Route::get('/recent', [SongController::class, 'recent']);
        Route::get('/last-modified', [SongController::class, 'lastModified']);
        Route::post('/batch', [SongController::class, 'batch']);
        Route::get('/stats', [SongController::class, 'stats']);
        Route::get('/{song}', [SongController::class, 'show']);
    });

});

// Rutas protegidas con JWT (admin desde Flutter)
Route::middleware('auth:api')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // CRUD Songs
    Route::get('/songs', [SongController::class, 'songs']);
    Route::get('/song/{song}', [SongController::class, 'show']);
    Route::post('/song/create', [SongController::class, 'store']);
    Route::put('/song/update/{song}', [SongController::class, 'update']);
    Route::delete('/song/delete/{song}', [SongController::class, 'destroy']);

    // Categories
    Route::get('/admin/categories', [CategoryController::class, 'adminIndex']);

    // Admin Stats
    Route::get('/admin/stats', [StatsController::class, 'adminStats']);
});

Route::get('/health', [SongController::class, 'health']);
