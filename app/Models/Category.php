<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con canciones
     */
    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class)
                    ->withPivot('order')
                    ->withTimestamps()
                    ->orderByPivot('order');
    }

    /**
     * Scope para obtener por slug
     */
    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Scope para ordenar
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
