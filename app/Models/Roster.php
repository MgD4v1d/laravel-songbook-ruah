<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Roster extends Model
{
    protected $fillable = [
        'month',
        'source_path',
        'status',
        'published_at'
    ];

    protected function casts(): array
    {
        return[
            'month' => 'date:Y-m-d',
            'published_at' => 'datetime'
        ];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(RosterEntry::class)
            ->orderBy('date')
            ->orderBy('time');
    }
}
