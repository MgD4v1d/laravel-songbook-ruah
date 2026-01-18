<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;


class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Cache::remember('categories:all', 3600, function(){
            return Category::ordered()
                ->withCount('songs')
                ->get();
        });

        return response()->json(
            CategoryResource::collection($categories)
        );
    }


    //* Obtener una categoria especifica
   public function show(string $slug): JsonResponse
    {
        $category = Cache::remember("category:{$slug}", 3600, function() use ($slug) {
            return Category::bySlug($slug)
                ->withCount('songs')
                ->firstOrFail();
        });

        return response()->json(
            new CategoryResource($category)
        );
    }


    /**
     * Última modificación
     */

    public function lastModified(Request $request)
    {

        $timestamp = Cache::remember('categories:last_modified_ts', 3600, function () {
            return Category::latest('updated_at')->value('updated_at')?->timestamp ?? now()->timestamp;
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
     * Categories Stats
     */

     public function stats(): JsonResponse
    {
        try {
            $stats = Cache::remember('categories:stats', 3600, function () {
                return [
                    'status' => 'OK',
                    'message' => 'categories stats success',
                    'categories_count' => Category::count(),
                    'categories' => Category::withCount('songs')
                        ->ordered()
                        ->get()
                        ->map(function($cat) {
                            return [
                                'name' => $cat->name,
                                'slug' => $cat->slug,
                                'songs_count' => $cat->songs_count,
                            ];
                        }),
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
}
