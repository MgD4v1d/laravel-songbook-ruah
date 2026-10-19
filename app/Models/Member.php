<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'email',
        'name',
        'active',
    ];


    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }


    protected function email(): Attribute
    {
        return Attribute::make(set: fn (string $value) => strtolower(trim($value)));
    }
}
