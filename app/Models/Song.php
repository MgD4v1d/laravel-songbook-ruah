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
            return $this->convertHtmlToBlocks($this->attributes['lyrics']);
        }

        return [];
    }
    /**
     * Convierte HTML a formato de bloques
     */
    private function convertHtmlToBlocks(string $html): array
    {
        if (empty(trim($html))) {
            return [];
        }

        // Remover tags HTML pero preservar estructura
        $html = $this->cleanHtml($html);
        
        // Convertir <strong>, <b> a **
        $html = preg_replace('/<(strong|b)>(.*?)<\/(strong|b)>/i', '**$2**', $html);
        
        // Convertir <em>, <i> a _
        $html = preg_replace('/<(em|i)>(.*?)<\/(em|i)>/i', '_$2_', $html);
        
        // Convertir <br> y <br/> a saltos de línea
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
        
        // Dividir por párrafos <p>
        $paragraphs = preg_split('/<\/?p>/i', $html);
        
        // Si no hay párrafos, dividir por dobles saltos de línea
        if (count($paragraphs) <= 1) {
            $paragraphs = explode("\n\n", $html);
        }

        // Filtrar vacíos y crear bloques
        $blocks = collect($paragraphs)
            ->map(fn($p) => trim(strip_tags($p)))
            ->filter(fn($p) => !empty($p))
            ->values()
            ->map(function($content, $index) {
                return [
                    'id' => time() + $index,
                    'type' => $this->detectBlockType($content, $index),
                    'content' => $content,
                    'label' => $this->generateBlockLabel($content, $index)
                ];
            })
            ->toArray();

        return $blocks;
    }

    /**
     * Limpia HTML preservando solo tags importantes
     */
    private function cleanHtml(string $html): string
    {
        // Remover scripts y styles
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        
        // Convertir entidades HTML
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $html;
    }

    /**
     * Detecta el tipo de bloque basado en el contenido
     */
    private function detectBlockType(string $content, int $index): string
    {
        $content = strtolower($content);
        
        // Patrones comunes de coros
        $chorusPatterns = [
            '/^coro/i',
            '/\(.*?x\s*\d+.*?\)/i',  // (x2), (x3)
            '/^\[coro\]/i',
            '/^estribillo/i',
            '/aleluya/i',
            '/gloria/i',
            '/hosanna/i',
        ];

        foreach ($chorusPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return 'chorus';
            }
        }

        // Patrones de puente
        $bridgePatterns = [
            '/^puente/i',
            '/^\[puente\]/i',
            '/^bridge/i',
        ];

        foreach ($bridgePatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return 'bridge';
            }
        }

        // Por defecto es estrofa
        return 'verse';
    }

    /**
     * Genera etiqueta automática para el bloque
     */
    private function generateBlockLabel(string $content, int $index): string
    {
        $type = $this->detectBlockType($content, $index);
        
        switch ($type) {
            case 'chorus':
                return 'Coro';
            case 'bridge':
                return 'Puente';
            case 'verse':
            default:
                // Contar cuántas estrofas hay antes
                return 'Estrofa ' . ($index + 1);
        }
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
