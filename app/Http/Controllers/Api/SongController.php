<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SongBatchRequest;
use App\Http\Resources\SongCollection;
use App\Http\Resources\SongMetadataResource;
use App\Http\Resources\SongResource;
use App\Models\Song;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class SongController extends Controller
{
    
    //* Lista de metada (sin lyrics) para flutter

    public function metadata(): JsonResponse
    {
        $songs = Song::select(['id', 'title', 'artist', 'key', 'created_at', 'updated_at'])
            ->alphabetical()
            ->get();

        return response()->json(
            SongMetadataResource::collection($songs)
        );
    }


    //* Canción completa con lyrics
    public function show(Song $song): SongResource
    {
        return new SongResource($song);
    }

    /**
     *  Múltiples canciones (batch) - Usando Form Request
     */

    public function batch(SongBatchRequest $request): JsonResponse
    {
        $songs = Song::whereIn('id', $request->validated()['ids'])->get();

        return response()->json(
            new SongCollection(SongResource::collection($songs))
        );
    }

    /**
     * Última modificación
     */

    public function lastModified(): JsonResponse
    {
        $lastModified = Song::max('updated_at') ?? now();
        return response()->json([
            'last_modified' => Carbon::parse($lastModified)->toISOString()
        ]);
    }

    /**
     * Health check
     */
    public function health(): JsonResponse
    {
        try{
            return response()->json([
                'status' => 'OK',
                'message' => 'status ok',
                'timestamp' => now()->toISOString(),
                'songs_count' => Song::count(),
                'laravel_version' => app()->version(),
            ]);
        } catch (\Throwable $e){
            return response()->json([
                'status' => 'ERROR',
                'message' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
                'songs_count' => Song::count(),
                'laravel_version' => app()->version(),
            ], 500);
        }
    }

}
