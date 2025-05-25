<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Transformers\FormValidationTransformer;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     *
     * @throws Exception
     */
    public function report(Exception $e): void
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @return Response|JsonResponse
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ValidationException) {
            return fractal($e->validator->errors())
                ->transformWith(new FormValidationTransformer)
                ->respond(422);
        }

        return parent::render($request, $e);
    }
}
