<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RosterEntryResource;
use App\Models\RosterEntry;
use Illuminate\Http\JsonResponse;

class RosterController extends Controller
{
    public function index(): JsonResponse
    {
        $choir      = config('services.roster.choir');
        $timezone   = config('services.roster.timezone');

        // Inicio del mes actual EN MÉRIDA, no en UTC
        $from = now($timezone)->startOfMonth()->toDateString();

        $entries = RosterEntry::query()
            ->join('rosters', 'rosters.id', '=', 'roster_entries.roster_id')
            ->where('rosters.status', 'published')
            ->where('roster_entries.date', '>=', $from)
            ->where('roster_entries.choir', 'like', '%' . $choir . '%')
            ->orderByDesc('rosters.month')          // el rol más reciente primero…
            ->get(['roster_entries.*'])
            ->unique(fn ($e) => $e->date->toDateString() . ' ' . $e->time) // …y se queda con su versión
            ->sortBy(fn ($e) => $e->date->toDateString() . ' ' . $e->time)
            ->values();

        return response()->json([
            'choir'    => $choir,
            'timezone' => $timezone,
            'entries'  => RosterEntryResource::collection($entries),
        ]);
    }
}
