<?php

namespace App\Observers;

use App\Models\Song;
use Illuminate\Support\Facades\Cache;

class SongObserver
{
    /**
     * Handle the Song "created" event.
     */
    public function created(Song $song): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Song "updated" event.
     */
    public function updated(Song $song): void
    {
        $this->clearCache();
        Cache::forget("song:{$song->id}");
    }

    /**
     * Handle the Song "deleted" event.
     */
    public function deleted(Song $song): void
    {
        $this->clearCache();
        Cache::forget("song:{$song->id}");
    }

    /**
     * Clear all song-related caches
     */
    private function clearCache(): void
    {
        Cache::forget('songs:metadata');
        Cache::forget('songs:recent');
        Cache::forget('songs:stats');
        Cache::forget('songs:last_modified');
    }
}
