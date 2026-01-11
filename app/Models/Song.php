<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Song extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'artist',
        'lyrics',
        'key',
        'rhythm',
        'tempo',
        'video_url'
    ];


    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con categorías
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)
                    ->withPivot('order')
                    ->withTimestamps()
                    ->orderByPivot('order');
    }


    /**
    * Scope para busquedas de texto completo
    * Usa FULLTEXT index de MySQL
    */

    public function scopeSearch(Builder $query, string $search): void
    {
        $searchTerm = '%' .$search . '%';

        $query->where(function($q) use ($searchTerm){
            $q->where('title', 'LIKE', $searchTerm)
              ->orWhere('artist', 'LIKE', $searchTerm)
              ->orWhere('lyrics', 'LIKE', $searchTerm);
        });
    }

    /**
     *  Scope para filtrar por tono/key
     */

    public function scopeByKey(Builder $query, string $key): void
    {
        $query->where('key', $key);
    }


    /**
     * Scope para ordenar alfabéticamente
     */
    public function scopeAlphabetical(Builder $query): void
    {
        $query->orderBy('title', 'asc');
    }

    /**
     * Scope para filtrar por categoría
     */
    public function scopeByCategory(Builder $query, $categoryId): void
    {
        $query->whereHas('categories', function($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }

    /**
     * Scope para filtrar por slug de categoría
     */
    public function scopeByCategorySlug(Builder $query, string $slug): void
    {
        $query->whereHas('categories', function($q) use ($slug) {
            $q->where('categories.slug', $slug);
        });
    }

}
