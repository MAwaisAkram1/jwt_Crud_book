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
     * register services in the config directory inside app.php to be bootstrapped to be used throwOut
     * the application.
     */
    public function boot()
    {
        // create a custom response for the success case
        Response::macro('success', function ($message = [], $status = 200){
            return response()->Json([
                'status' => 'success',
                'message' => $message,

            ], $status);
        });
        // create a custom response for the fail case
        Response::macro('fail', function ($message = "", $status = 400){
            return response()->Json([
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
