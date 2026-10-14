<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RosterEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'date' => $this->date->toDateString(),
            'time' => substr($this->time, 0, 5),
            'choir' => $this->choir,
            'celebration' => $this->celebration
        ];
    }
}
