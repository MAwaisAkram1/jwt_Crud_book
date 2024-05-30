<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    
    // public function render($request, Throwable $exception) {
    //     if ($request instanceof ValidationException) {
    //         return Response::fail();
    //     }
    // }

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
