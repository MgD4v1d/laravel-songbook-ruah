<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
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

    public function lastModified()
    {
        $lastModified = Cache::remember('categories:last_modified', 3600, function () {
            return Category::max('updated_at');
        });

        if (empty($lastModified)) {
            $lastModified = now();
        }

        $lastModifiedDate = Carbon::parse($lastModified)->toRfc7231String();

        return response('', 200)->header('Last-Modified', $lastModifiedDate);
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
