<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    /**
     * Admin Stats - Dashboard statistics
     */
    public function adminStats(): JsonResponse
    {
        $now = Carbon::now();

        // Canciones agregadas este mes
        $songsThisMonth = Song::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->count();

        // Última canción agregada
        $lastSong = Song::latest('created_at')->first(['id', 'title', 'artist', 'created_at']);

        // Canciones sin categoría
        $songsWithoutCategory = Song::doesntHave('categories')->count();

        // Distribución por categoría
        $categoriesWithSongs = Category::withCount('songs')
            ->ordered()
            ->get()
            ->map(fn($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'songs_count' => $cat->songs_count,
            ]);

        // Server & API stats
        $serverStats = $this->getServerStats();

        return response()->json([
            'data' => [
                'songs_count' => Song::count(),
                'categories_count' => Category::count(),
                'users_count' => User::count(),
                'songs_this_month' => $songsThisMonth,
                'songs_without_category' => $songsWithoutCategory,
                'last_song' => $lastSong ? [
                    'id' => $lastSong->id,
                    'title' => $lastSong->title,
                    'artist' => $lastSong->artist,
                    'created_at' => $lastSong->created_at->toISOString(),
                ] : null,
                'categories' => $categoriesWithSongs,
            ],
            'server' => $serverStats,
        ]);
    }

    /**
     * Get server and API statistics
     */
    private function getServerStats(): array
    {
        // Database status
        $dbStatus = 'OK';
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbStatus = 'ERROR';
        }

        // Cache status
        $cacheStatus = 'OK';
        try {
            Cache::put('health_check', true, 1);
            Cache::forget('health_check');
        } catch (\Exception $e) {
            $cacheStatus = 'ERROR';
        }

        // Memory usage
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);

        return [
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'database' => [
                'status' => $dbStatus,
                'driver' => config('database.default'),
            ],
            'cache' => [
                'status' => $cacheStatus,
                'driver' => config('cache.default'),
            ],
            'memory' => [
                'usage' => $this->formatBytes($memoryUsage),
                'peak' => $this->formatBytes($memoryPeak),
                'limit' => ini_get('memory_limit'),
            ],
            'server' => [
                'os' => PHP_OS,
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'CLI',
            ],
            'timestamp' => now()->toISOString(),
            'timezone' => config('app.timezone'),
        ];
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
