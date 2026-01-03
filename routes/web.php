<?php

use App\Http\Controllers\Admin\AdminSongController;
use App\Http\Controllers\ProfileController;
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



Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function(){
        $stats = [
            'total_songs' => Song::count(),
            'today_songs' => Song::whereDate('created_at', today())->count(),
            'api_status'  => 'active' 
        ];

        return Inertia::render('Dashboard', compact('stats'));
    })->name('dashboard');

    // Rutas de canciones (admin)
    Route::resource('songs', AdminSongController::class);
    

    
});

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

require __DIR__.'/auth.php';
