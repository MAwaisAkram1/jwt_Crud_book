<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Response;
class UserAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $key=null)
    {
        try {
            if ($key === 'register') {
                app(RegisterRequest::class);

            } else if ($key === 'login') {
                app(LoginRequest::class);
            }
            return $next($request);
        } catch (ValidationException $e) {
            return Response::fail("Validation Error", 400);
            // return response()->json([
            //     'errors' => $e->errors(),
            //     // 'message' => "error"
            // ], 401);
        }

    }
}
