<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Support\Facades\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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

    public function render($request , Throwable $e) {
        if ($e instanceof NotFoundHttpException) {
            return Response::fail("Resource not Found", 404);
        }

        if ($e instanceof ModelNotFoundException) {
            return Response::fail("Resource not Found", 404);
        }
        if ($e instanceof AuthenticationException) {
            return Response::fail("Unauthenticated", 401);
        }
        if ($e instanceof ValidationException) {
            return Response::fail($e->errors(), 422);
        }
        return Response::fail("An unexpected error occurred", 500);
    }
}
