<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;



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

    /**
     * Busca una categoría por nombre (case-insensitive) o slug.
     * No crea una nueva si no encuentra coincidencia.
     */
    public static function findByNameOrSlug(string $value): ?self
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        return static::where(function ($query) use ($value) {
            $query->whereRaw('LOWER(name) = ?', [mb_strtolower($value)])
                ->orWhere('slug', Str::slug($value));
        })->first();
    }
}
