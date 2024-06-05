<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogBookOperations
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        Log::channel('book_operations')->info('Incoming Request', [
            'method' => $request->method(),
            'url' => $request->url(),
            'body' => $request->all(),
        ]);
        $response = $next($request);

        Log::channel('book_operations')->info('Outgoing Request', [
            'status' => $response->status(),
            'response' => $response->getContent(),
        ]);
        return $response;
    }
}
