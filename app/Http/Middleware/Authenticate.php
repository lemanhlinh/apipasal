<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class Authenticate extends Middleware
{

    public function handle(Request $request, Closure $next)
    {
        try {
            // Kiểm tra xem có token trong header Authorization không
            $token = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            // Nếu không có hoặc token không hợp lệ, trả về lỗi 401 Unauthorized
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Nếu token hợp lệ, tiếp tục xử lý và cho phép truy cập
        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
