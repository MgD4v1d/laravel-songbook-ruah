<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TelegramSongRequest;
use App\Http\Resources\SongResource;
use App\Models\Category;
use App\Models\Song;
use App\Services\LyricsBlockParser;
use App\Services\TelegramSongTextParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TelegramSongController extends Controller
{
    public function store(TelegramSongRequest $request): JsonResponse
    {
        $parsed = TelegramSongTextParser::parse($request->validated()['text']);

        $lyricsBlocks = LyricsBlockParser::parse($parsed['lyrics']);

        if (empty($lyricsBlocks)) {
            throw ValidationException::withMessages([
                'text' => ['No se pudo interpretar la letra de la canción.'],
            ]);
        }

        $song = Song::create([
            'title' => $parsed['title'],
            'artist' => $parsed['artist'] ?? '',
            'lyrics_blocks' => $lyricsBlocks,
        ]);

        $category = $parsed['category']
            ? Category::findByNameOrSlug($parsed['category'])
            : null;

        if ($category) {
            $song->categories()->sync([$category->id]);
        }

        $song->touch();

        return response()->json([
            'song' => new SongResource($song->load('categories:id,name,slug')),
            'categoryMatched' => (bool) $category,
        ], 201);
    }
}
