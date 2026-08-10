<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBotApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-BOT-API-KEY');
        $validKey = config('app.bot_api_key');

        if (! $validKey || ! $apiKey || ! hash_equals($validKey, $apiKey)) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Unauthorized - Invalid Bot API Key',
            ], 401);
        }

        return $next($request);
    }
}
