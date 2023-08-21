<?php

namespace App\Exceptions;

use App\Traits\ApiResponder;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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

    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return ApiResponder::error(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $e->errors()
            );
        }

        if ($e instanceof ApiException) {
            return ApiResponder::error(
                $e->getMessage(),
                $e->getCode()
            );
        }

        if ($e instanceof NotFoundHttpException) {
            return ApiResponder::error(
                'Not found',
                Response::HTTP_NOT_FOUND
            );
        }

        return ApiResponder::error($e->getMessage());
    }
}
