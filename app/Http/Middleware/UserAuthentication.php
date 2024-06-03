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
            //check if the http request role is register then it will call the RegisterRequest
            // validate the request and return to the UserCOntroller for the registration process
            if ($key === 'register') {
                app(RegisterRequest::class);

                // if the http request role is login then it will call the LoginRequest validate the
                // request and return to the UserController for the login process
            } else if ($key === 'login') {
                app(LoginRequest::class);
            }
            return $next($request);
            
            // catch any exceptions that might have been thrown by the register and login methods
        } catch (ValidationException $e) {
            return Response::fail("Validation Error", 400);
            // return response()->json([
            //     'errors' => $e->errors(),
            //     'message' => "error"
            // ], 401);
        }

    }
}
