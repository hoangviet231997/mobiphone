<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
class CheckToken
{
    public function handle($request, Closure $next)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
        }catch (JWTException $e) {
            return response()->json([
                'message' => 'Unauthorized error.',
                'status_code' => 401
            ],401);
        }
        return $next($request);
    }


}
