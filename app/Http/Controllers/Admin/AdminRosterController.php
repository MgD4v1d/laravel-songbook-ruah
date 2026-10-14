<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Roster;
use App\Services\RosterReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class AdminRosterController extends Controller
{
    public function index(): Response
    {
        $rosters = Roster::withCount(['entries as choir_count' => fn ($q) => $q->where('choir', 'like', '%' . config('services.roster.choir') . '%')])
            ->orderByDesc('month')->get();

        return Inertia::render('Admin/Rosters/Index',[
            'rosters' => $rosters
        ]);
    }


    public function create(): Response
    {
        return Inertia::render('Admin/Rosters/Create');
    }


    public function store(Request $request, RosterReader $reader)
    {
        $request->validate([
        'file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ], [
            'file.required' => 'Selecciona el archivo del rol.',
            'file.mimes'    => 'El rol debe ser PDF o imagen (JPG, PNG, WEBP).',
        ]);

        set_time_limit(120); // Gemini puede tardar más de 30 s

        $file = $request->file('file');
        $path = $file->store('rosters'); // storage/app/private/rosters

        try {
            $data = $reader->read(Storage::path($path), $file->getMimeType());
        } catch (RuntimeException $e) {
            Storage::delete($path);
            return back()->withErrors(['file' => $e->getMessage()]);
        }

        $roster = DB::transaction(function () use ($data, $path) {
            $existing = Roster::whereDate('month', $data['month'] . '-01')->first();
            if ($existing) {
                Storage::delete($existing->source_path); // el archivo anterior ya no sirve
            }

            $roster = Roster::updateOrCreate(
                ['month' => $data['month'] . '-01'],
                ['source_path' => $path, 'status' => 'draft', 'published_at' => null],
            );

            $roster->entries()->delete();
            $roster->entries()->createMany($data['entries']);

            return $roster;
        });

        return redirect()->route('admin.rosters.edit', $roster)
        ->with('success', 'Rol leído. Revísalo antes de publicar.');
    }

    public function edit(Roster $roster): Response
    {

        $roster->load('entries');

        return Inertia::render('Admin/Rosters/Edit', [
            'roster'    => $roster,
            'choir'     => config('services.roster.choir'),
            'sourceUrl' => route('admin.rosters.source', $roster),
        ]);
    }

    public function update(Request $request, Roster $roster)
    {
        $validate = $request->validate([
            'entries'               => 'array',
            'entries.*.date'        => 'required|date_format:Y-m-d',
            'entries.*.time'        => 'required|date_format:H:i',
            'entries.*.choir'       => 'required|string|max:100',
            'entries.*.celebration' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($roster, $validate) {
            $roster->entries()->delete();
            $roster->entries()->createMany($validate['entries'] ?? []);
        });

        // Actualiza updated_at para que la API avise a la app del cambio
        $roster->touch();

        return back()->with('success', 'Cambios guardados.');
    }

    public function publish(Roster $roster)
    {
        $roster->status = 'published';
        $roster->published_at = now();
        $roster->save();

        return redirect()->route('admin.rosters.index')
            ->with('success', 'Rol publicado. Ya está disponible en la app.');
    }

    public function source(Roster $roster)
    {
        return Storage::response($roster->source_path);
    }

    public function destroy(Roster $roster)
    {
        
        Storage::delete($roster->source_path); // el archivo anterior ya no sirve
        $roster->delete();

        return redirect()->route('admin.rosters.index')
            ->with('success', "🗑️ Rol eliminado exitosamente");
    }

}
