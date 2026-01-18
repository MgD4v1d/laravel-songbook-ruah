<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SongBatchRequest;
use App\Http\Resources\SongCollection;
use App\Http\Resources\SongMetadataResource;
use App\Http\Resources\SongResource;
use App\Models\Category;
use App\Models\Song;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SongController extends Controller
{
    
    //* Lista de metadata (sin lyrics) para flutter

    public function metadata(): JsonResponse
    {
        $songs = Cache::remember('songs:metadata', 3600, function(){
            return Song::with('categories:id,name,slug')
            ->select(['id', 'title', 'artist', 'key', 'video_url', 'created_at', 'updated_at'])
            ->alphabetical()
            ->get();
        });

        return response()->json(
            SongMetadataResource::collection($songs)
        );
    }


    //* Lista de metadata filtrada por categoria (para Flutter)
    public function metadataByCategory(string $categorySlug): JsonResponse
    {
        $data = Cache::remember("songs:metadata:category:{$categorySlug}", 3600, function () use ($categorySlug) {
            // Verificar que la categoría existe
            $category = Category::where('slug', $categorySlug)->first();

            if (!$category) {
                return null;
            }

            $songs = Song::select(['id', 'title', 'artist', 'key', 'video_url','created_at', 'updated_at'])
                ->byCategorySlug($categorySlug)
                ->alphabetical()
                ->get();

            return [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ],
                'songs' => $songs
            ];
        });

        if ($data === null) {
            return response()->json([
                'message' => 'Category not found',
                'slug' => $categorySlug
            ], 404);
        }

        return response()->json([
            'category' => $data['category'],
            'songs' => SongMetadataResource::collection($data['songs'])
        ]);
    }


    //* Canción completa con lyrics
    public function show(Song $song): SongResource
    {
        $cachedSong = Cache::remember("song:{$song->id}", 3600, function () use ($song) {
            return $song->load('categories:id,name,slug');
        });

        return new SongResource($cachedSong);
    }

    /**
     *  Múltiples canciones (batch) - Usando Form Request
     */

    public function batch(SongBatchRequest $request): JsonResponse
    {
        $songs = Song::with('categories:id,name,slug')
            ->whereIn('id', $request->validated()['ids'])
            ->get();

        return response()->json(
            new SongCollection(SongResource::collection($songs))
        );
    }


    /**
     *  Ultimas Canciones Agregadas
     */


     public function recent(): JsonResponse
     {
        $songs = Cache::remember('songs:recent', 3600, function(){
            return Song::select(['id', 'title', 'artist', 'key', 'video_url','created_at', 'updated_at'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        });

        return response()->json(
            SongMetadataResource::collection($songs)
        );
     }


    /**
     * Última modificación
     */

    // public function lastModified()
    // {
    //     $lastModified = Cache::remember('songs:last_modified', 3600, function () {
    //         return Song::max('updated_at');
    //     });

    //     if (empty($lastModified)) {
    //         $lastModified = now();
    //     }

    //     $lastModifiedDate = Carbon::parse($lastModified)->toRfc7231String();

    //     return response('', 200)->header('Last-Modified', $lastModifiedDate);
    // }

    public function lastModified(Request $request)
    {
        $timestamp = Cache::remember('songs:last_modified_ts', 3600, function () {
            return Song::latest('updated_at')->value('updated_at')?->timestamp ?? now()->timestamp;
        });

        $lastModifiedDate = Carbon::createFromTimestamp($timestamp);
        $ifModifiedSince = $request->header('If-Modified-Since');

        if ($ifModifiedSince) {
            // Convertimos a timestamp para comparar peras con peras
            $clientTimestamp = is_numeric($ifModifiedSince) 
                ? (int)$ifModifiedSince 
                : Carbon::parse($ifModifiedSince)->timestamp;

            if ($timestamp <= $clientTimestamp) {
                return response('', 304);
            }
        }

        return response()->json(['last_modified' => $timestamp])
                        ->setLastModified($lastModifiedDate)
                        ->header('Cache-Control', 'public, max-age=60');
    }

    /**
     * Song Stats
     */

     public function stats(): JsonResponse
     {
        try {
            $stats = Cache::remember('songs:stats', 3600, function () {
                return [
                    'status' => 'OK',
                    'message' => 'status API success',
                    'songs_count' => Song::count(),
                    'last_modified' => Song::max('updated_at'),
                ];
            });

            return response()->json($stats);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
     }


    /**
     * Health check
     */
    public function health(): JsonResponse
    {
        try {
            return response()->json([
                'status' => 'OK',
                'message' => 'status ok',
                'timestamp' => now()->toISOString(),
                'laravel_version' => app()->version(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
                'laravel_version' => app()->version(),
            ], 500);
        }
    }

}
