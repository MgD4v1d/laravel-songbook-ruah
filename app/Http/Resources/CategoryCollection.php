<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'categories' => $this->collection
        ];
    }


    public function with(Request $request): array
    {
        return [
            'meta' => [
                'total' => $this->collection->count(),
                'timestamp' => now()->toISOString(),
            ],
        ];
    }
}
