<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Song;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminSongController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $filter = $request->input('filter', 'all');
        $categoryId = $request->input('category');

        $songs = Song::query()
            ->with('categories:id,name')
            ->when($search, function ($query, $search) {
                $query->search($search);
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            })
            ->when($filter === 'recent', function ($query) {
                $query->orderBy('created_at', 'desc');
            }, function ($query) {
                $query->alphabetical();
            })
            ->paginate(10)
            ->withQueryString();

        $categories = Category::ordered()->get(['id', 'name']);

        return Inertia::render('Admin/Songs/Index', [
            'songs' => $songs,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'filter' => $filter,
                'category' => $categoryId,
            ],
        ]);
    }

    public function create(): Response
    {
        $categories = Category::ordered()->get(['id', 'name']);

        return Inertia::render('Admin/Songs/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'artist' => 'required|string|min:3|max:255',
            'lyrics_blocks' => 'required|array|min:1',
            'lyrics_blocks.*.id' => 'nullable',
            'lyrics_blocks.*.type' => 'required|in:verse,chorus,bridge',
            'lyrics_blocks.*.content' => 'required|string',
            'lyrics_blocks.*.label' => 'nullable|string',
            'key' => 'nullable|string|max:150',
            'rhythm' => 'nullable|string|max:150',
            'tempo' => 'nullable|integer|min:20|max:240',
            'video_url' => 'nullable|url|max:500',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ], [
            'title.required' => 'Titulo obligatorio',
            'artist.required' => 'Artista obligatorio',
            'lyrics_blocks.required' => 'La letra es obligatoria',
        ]);

        $categories = $validated['categories'] ?? [];
        unset($validated['categories']);

        $song = Song::create($validated);

        if (!empty($categories)) {
            $song->categories()->attach($categories);
        }

        return redirect()->route('admin.songs.index')
            ->with('success', "Canción '{$song->title}' creada exitosamente");
    }

    public function show(Song $song): Response
    {
        return Inertia::render('Admin/Songs/Show', [
            'song' => $song,
        ]);
    }

    public function edit(Song $song): Response
    {
        $categories = Category::ordered()->get(['id', 'name']);
        $song->load('categories');

        return Inertia::render('Admin/Songs/Edit', [
            'song' => $song,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Song $song)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'artist' => 'required|string|min:3|max:255',
            'lyrics_blocks' => 'required|array|min:1',
            'lyrics_blocks.*.id' => 'nullable',
            'lyrics_blocks.*.type' => 'required|in:verse,chorus,bridge',
            'lyrics_blocks.*.content' => 'required|string',
            'lyrics_blocks.*.label' => 'nullable|string',
            'key' => 'nullable|string|max:150',
            'rhythm' => 'nullable|string|max:150',
            'tempo' => 'nullable|integer|min:20|max:240',
            'video_url' => 'nullable|url|max:500',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ], [
            'title.required' => 'Titulo obligatorio',
            'artist.required' => 'Artista obligatorio',
            'lyrics_blocks.required' => 'La letra es obligatoria',
        ]);

        // Normalizar estrofas (agrupar <p> en estrofas con <br>) ANTES de purificar
        // $validated['lyrics'] = $this->normalizeLyricsHtml($validated['lyrics']);
        // $validated['lyrics'] = clean($validated['lyrics']);

        $categories = $validated['categories'] ?? [];
        unset($validated['categories']);

        $song->update($validated);

        // Sincronizar categorías (elimina las que no están y agrega las nuevas)
        $song->categories()->sync($categories);

        // Tocar el modelo para actualizar updated_at y disparar el observer
        $song->touch();

        return redirect()->route('admin.songs.index')
            ->with('success', "Canción '{$song->title}' actualizada exitosamente");
    }

    public function destroy(Song $song)
    {
        $title = $song->title;
        $song->delete();

        return redirect()->route('admin.songs.index')
            ->with('success', "🗑️ Canción '{$title}' eliminada exitosamente");
    }
}
