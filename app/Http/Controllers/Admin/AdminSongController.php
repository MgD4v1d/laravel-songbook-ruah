<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class AdminSongController extends Controller
{
    public function index(Request $request) : Response
    {
        $search = $request->input('search');
        $songs = Song::query()
            ->when($search, function($query, $search){
                $query->search($search);
            })
            ->alphabetical()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Songs/Index', [
            'songs' => $songs,
            'filters' => ['search' => $search]
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Songs/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'artist' => 'required|string|min:3|max:255',
            'lyrics' => 'required|string',
            'key' => 'nullable|string|max:150',
            'rhythm' => 'nullable|string|max:150',
            'tempo' => 'nullable|integer|min:20|max:240',
            'video_url' => 'nullable|url|max:500'
        ], [
            'title.required' => 'Titulo obligatorio',
            'artist.required' => 'Artista obligatorio',
            'lyrics.required' => 'La letra es obligatoria'
        ]);


        Log::info('Lyrics recibidas:', ['lyrics' => $validated['lyrics']]);

        $validated['lyrics'] = str_replace("\r\n", "\n", $validated['lyrics']);

        Song::create($validated);

        return redirect()->route('admin.songs.index')
            ->with('success', 'Canción creada exitosamente');
    }

    public function show(Song $song): Response
    {
        return Inertia::render('Admin/Songs/Show', [
            'song' => $song
        ]);
    }

    public function edit(Song $song): Response
    {
        return Inertia::render('Admin/Songs/Edit', [
            'song' => $song
        ]);
    }

    public function update(Request $request, Song $song)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'artist' => 'required|string|min:3|max:255',
            'lyrics' => 'required|string',
            'key' => 'nullable|string|max:150',
            'rhythm' => 'nullable|string|max:150',
            'tempo' => 'nullable|integer|min:20|max:240',
            'video_url' => 'nullable|url|max:500'
        ], [
            'title.required' => 'Titulo obligatorio',
            'artist.required' => 'Artista obligatorio',
            'lyrics.required' => 'La letra es obligatoria'
        ]);

        Log::info('Lyrics recibidas:', ['lyrics' => $validated['lyrics']]);

        $validated['lyrics'] = str_replace("\r\n", "\n", $validated['lyrics']);


        $song->update($validated);

        return redirect()->route('admin.songs.index')
            ->with('success', 'Cancion actualizada exitosamente');
    }


    public function destroy(Song $song)
    {
        $song->delete();
        
        return redirect()->route('admin.songs.index')
            ->with('success', 'Canción eliminada exitosamente');
    }


}
