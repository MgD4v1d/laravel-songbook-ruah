<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        $this->clearCache();
        Cache::forget("category:{$category->slug}");
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        $this->clearCache();
        Cache::forget("category:{$category->slug}");
        Cache::forget("songs:metadata:category:{$category->slug}");
    }

    /**
     * Clear all category-related caches
     */
    private function clearCache(): void
    {
        $keys = [
            'categories:all',
            'categories:stats',
            'categories:last_modified',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
