<?php

namespace App\Models;

use App\Services\LyricsBlockParser;
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
     * Convierte bloques de vuelta a HTML (para compatibilidad)
     */
    public function blocksToHtml(): string
    {
        if (!$this->lyrics_blocks) {
            return $this->lyrics ?? '';
        }

        return collect($this->lyrics_blocks)
            ->map(function($block) {
                $content = $block['content'] ?? '';
                
                // Convertir markdown a HTML
                $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);
                $content = preg_replace('/_(.*?)_/', '<em>$1</em>', $content);
                $content = nl2br($content);
                
                $type = $block['type'] ?? 'verse';
                $label = $block['label'] ?? '';
                
                return "<div class='lyrics-block lyrics-{$type}'>"
                     . ($label ? "<strong>{$label}</strong><br>" : '')
                     . $content
                     . "</div>";
            })
            ->join("\n\n");
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
    public function scopeByCategory(Builder $query, int $categoryId): void
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

    /**
     * Obtener estadísticas de bloques
     */
    public function getBlockStatsAttribute(): array
    {
        if (!$this->lyrics_blocks) {
            return [
                'total' => 0,
                'verses' => 0,
                'choruses' => 0,
                'bridges' => 0,
            ];
        }

        $blocks = collect($this->lyrics_blocks);

        return [
            'total' => $blocks->count(),
            'verses' => $blocks->where('type', 'verse')->count(),
            'choruses' => $blocks->where('type', 'chorus')->count(),
            'bridges' => $blocks->where('type', 'bridge')->count(),
        ];
    }

}
