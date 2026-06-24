<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authenticates server-to-server sync requests from the PawsNation storefront
 * using a shared bearer token (timing-safe comparison).
 */
class VerifySyncToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('sync.token');
        $provided = (string) $request->bearerToken();

        if ($expected === '' || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
