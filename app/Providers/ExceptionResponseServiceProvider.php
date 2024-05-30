<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ExceptionResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($message = "", $status = 200){
            return response()->json([
                'status' => 'success',
                'message' => $message,
                
            ], $status);
        });

        Response::macro('fail', function ($message = "", $status = 400){
            return response()->json([
                'status' => 'Failed',
                'message' => $message,
                
            ], $status);
        });
        // Response::macro('exceptionHandler', function ($exception){
        //     if ($exception instanceof NotFoundHttpException){
        //         return response()->json([
        //             'error' => 'Resource not found',
        //         ], 404);
        //     }

        //     if ($exception instanceof ForbiddenHttpException) {
        //         return response()->json([
        //             'error' => 'Forbidden',
        //         ], 403);
        //     }

        //     if ($exception instanceof  AuthenticationException) {
        //         return response()->json([
        //             'error' => 'Unauthorized',
        //         ], 401);
        //     }

        //     if ($exception instanceof ValidationException) {
        //         return response()->json([
        //             'errors' => 'Validation failed',
        //         ], 422);
        //     }

        //     return response()->json([
        //         'error' => 'An unexpected error occurred'
        //     ], 500);
        // });
    }
}
