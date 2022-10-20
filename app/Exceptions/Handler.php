<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
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

    /**
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof BadArgumentException) {
            $error = [
                'code' => Response::HTTP_BAD_REQUEST,
                'error' => $e->getMessage(),
                'descriptions' => $e->getErrorDescriptions(),
            ];
            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            $error = [
                'code' => Response::HTTP_NOT_FOUND,
                'error' => 'Not found',
            ];
            return new JsonResponse($error, Response::HTTP_NOT_FOUND);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            $error = [
                'code' => Response::HTTP_METHOD_NOT_ALLOWED,
                'error' => $e->getMessage(),
            ];
            return new JsonResponse($error, Response::HTTP_METHOD_NOT_ALLOWED);
        }

        return parent::render($request, $e);
    }
}
