<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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


}
