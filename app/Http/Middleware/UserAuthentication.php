<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Validation\ValidationException;

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
            return response()->json([
                'errors' => $e->errors(),
            ], 401);
        }

    }
}
