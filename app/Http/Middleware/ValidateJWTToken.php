<?php

namespace App\Http\Middleware;

use App\Helper\TodoResponse;
use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ValidateJWTToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user) {
                TodoResponse::error('Unauthorized', 401);
            }
        } catch (JWTException $e) {
            TodoResponse::error('Unauthorized or Token is missing', 401);
        }

        return $next($request);
    }
}
