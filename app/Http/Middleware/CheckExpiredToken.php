<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class CheckExpiredToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // get token
        $token = $request->bearerToken();

        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);

            if ($accessToken) {
                // check token het han
                if (Carbon::now()->greaterThan($accessToken->expires_at)) {
                    // del
                    $accessToken->delete();

                    return response()->json(['message' => 'Hết hạn phiên đăng nhập.'], 401);
                }
            }
        }

        return $next($request);
    }
}
