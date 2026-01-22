<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminSongController;
use App\Http\Controllers\Admin\ProfileController;
use App\Models\Song;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/aviso-de-privacidad', function () {
    return Inertia::render('AvisoDePrivacidad');
})->name('aviso-de-privacidad');



Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function(){
        $stats = [
            'total_songs' => Song::count(),
            'today_songs' => Song::whereDate('created_at', today())->count(),
            'api_status'  => 'active' 
        ];

        return Inertia::render('Dashboard', compact('stats'));
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de canciones (admin)
    Route::resource('songs', AdminSongController::class);
    // Rutas de categorías (admin)
    Route::resource('categories', AdminCategoryController::class);
    
    
    
});
    
    

require __DIR__.'/auth.php';
