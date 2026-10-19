<?php

namespace App\Http\Middleware;

use App\Models\Member;
use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\JWT\IdTokenVerifier;
use Symfony\Component\HttpFoundation\Response;

class EnsureChoirMember
{

    public function __construct(private IdTokenVerifier $verifier){}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['code' => 'unauthenticated', 'message' => 'Falta el token.'], 401);
        }

        try {
            $payload = $this->verifier->verifyIdToken($token)->payload();
        } catch (\Throwable) {
            return response()->json(['code' => 'invalid_token', 'message' => 'Token inválido o vencido.'], 401);
        }

        // Sin email verificado no sabemos quién es (Google Sign-In siempre lo verifica)
        $email = strtolower($payload['email'] ?? '');
        if ($email === '' || ! ($payload['email_verified'] ?? false)) {
            return response()->json(['code' => 'invalid_token', 'message' => 'Token sin email verificado.'], 401);
        }

        $isMember = Member::where('email', $email)->where('active', true)->exists();

        if (! $isMember) {
            return response()->json(['code' => 'not_member', 'message' => 'Tu cuenta no tiene acceso.'], 403);
        }

        return $next($request);
    }
}
