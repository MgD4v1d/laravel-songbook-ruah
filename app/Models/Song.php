<?php

namespace App\Models;

use App\Services\LyricsBlockParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Song extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'title',
        'artist',
        'lyrics',
        'lyrics_blocks',
        'key',
        'rhythm',
        'tempo',
        'video_url'
    ];


    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'lyrics_blocks' => 'array'
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
     * Boot del modelo - Auto-generar lyrics desde blocks
     */
    protected static function boot()
    {
        parent::boot();

        // Antes de guardar, sincronizar lyrics desde lyrics_blocks
        static::saving(function ($song) {
            if ($song->lyrics_blocks && is_array($song->lyrics_blocks)) {
                $song->lyrics = $song->blocksToPlainText();
            }
        });
    }

    /**
     * Convierte los bloques a texto plano para búsquedas
     */
    public function blocksToPlainText(): string
    {
        if (!$this->lyrics_blocks) {
            return $this->lyrics ?? '';
        }

        return collect($this->lyrics_blocks)
            ->map(function($block) {
                $content = $block['content'] ?? '';
                // Remover acordes [Am], [C], [G7], etc.
                $content = preg_replace('/\[[^\]]+\]/', '', $content);
                // Remover marcadores de formato
                $content = preg_replace('/\*\*(.*?)\*\*/s', '$1', $content);
                $content = preg_replace('/_(.*?)_/s', '$1', $content);
                return $content;
            })
            ->filter()
            ->join("\n\n");
    }

    /**
     * Accessor para obtener bloques con fallback
     */
    public function getLyricsBlocksAttribute($value)
    {
        if ($value) {
            $blocks = json_decode($value, true);
            if (is_array($blocks) && count($blocks) > 0) {
                return $blocks;
            }
        }

        if ($this->attributes['lyrics'] ?? null) {
            return LyricsBlockParser::parse($this->attributes['lyrics']);
        }


        return [];
    }
    


    /**
    * Scope para Búsqueda con LIKE en título, artista y letra.
    * 
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
     * Scope para ordenar alfabéticamente
     */
    public function scopeAlphabetical(Builder $query): void
    {
        $query->orderBy('title', 'asc');
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
