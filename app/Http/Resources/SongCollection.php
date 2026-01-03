<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SongCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'songs' => $this->collection
        ];
    }

    /**
    * Get additional data that should be returned with thel resource array
    */

    public function with(Request $request): array
    {
        return [
            'meta' => [
                'total' => $this->collection->count(),
                'timestamp' => now()->toISOString()
            ],
        ];
    }
}
