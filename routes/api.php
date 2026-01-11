<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SongController;
use Illuminate\Support\Facades\Route;


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



Route::get('/health', [SongController::class, 'health']);
