<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Song;
use App\Observers\CategoryObserver;
use App\Observers\SongObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());

        Vite::prefetch(concurrency: 3);
        Song::observe(SongObserver::class);
        Category::observe(CategoryObserver::class);
    }
}
