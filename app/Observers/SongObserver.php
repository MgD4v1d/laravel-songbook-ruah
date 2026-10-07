<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\Song;
use Illuminate\Support\Facades\Cache;

class SongObserver
{
    /**
     * Handle the Song "created" event.
     */
    public function created(Song $song): void
    {
        \Log::info('SongObserver: created event fired', ['song_id' => $song->id]);
        $this->clearCache();
        $this->clearCategoryCaches($song);
    }

    /**
     * Handle the Song "updated" event.
     */
    public function updated(Song $song): void
    {
        \Log::info('SongObserver: updated event fired', ['song_id' => $song->id]);
        $this->clearCache();
        Cache::forget("song:{$song->id}");
        $this->clearCategoryCaches($song);
    }

    /**
     * Handle the Song "deleted" event.
     */
    public function deleted(Song $song): void
    {
        \Log::info('SongObserver: deleted event fired', ['song_id' => $song->id]);
        $this->clearCache();
        Cache::forget("song:{$song->id}");
        Cache::put('songs:last_modified_ts', now()->timestamp, 3600);
        $this->clearCategoryCaches($song);
    }

    /**
     * Clear all song-related caches
     */
    private function clearCache(): void
    {
        $keys = [
            'songs:metadata',
            'songs:recent',
            'songs:stats',
            'songs:last_modified_ts',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
            \Log::info("Cache cleared: {$key}");
        }
    }

    /**
     * Clear category-specific song caches
     */
    private function clearCategoryCaches(Song $song): void
    {
        // Borrar el cache de metadata por cada categoría asociada
        foreach (Category::pluck('slug') as $slug) {
            Cache::forget("songs:metadata:category:{$slug}");
        }
    }
}
