<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');
        $validKey = config('app.api_key');

        // Usamos hash_equals para una comparación segura
        if (!$apiKey || !hash_equals($validKey, $apiKey)) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Unauthorized - Invalid API Key', // Corregido el typo
            ], 401);
        }

        return $next($request);
    }
}
