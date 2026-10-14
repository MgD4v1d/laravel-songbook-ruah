<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RosterEntry extends Model
{
    protected $fillable = [
        'date',
        'time',
        'choir',
        'celebration',
        'roster_id'
    ];


    protected function casts(): array
    {
        return[
            'date' => 'date:Y-m-d',
        ];
    }


    public function roster(): BelongsTo
    {
        return $this->belongsTo(Roster::class);
    }
}
