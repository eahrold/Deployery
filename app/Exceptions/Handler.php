<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $e)
    {
        // If the request wants JSON (AJAX doesn't always want JSON)
        if ($request->is('api/*'))
        {
            return $this->renderApiResponse($request, $e);
        }
        return parent::render($request, $e);
    }

    /**
     * Render an exception into an HTTP response for an api endpoint.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function renderApiResponse($request, Exception $e)
    {
        $message = $e->getMessage();
        $errors = isset($e->errors) ? $e->errors() : [];
        $status = 500; // This is just the default...

        $response = compact('message', 'errors');

        if (config('app.debug'))
        {
            $response['exception'] = get_class($e);
            $response['trace'] = $e->getTrace();
        }

        // If this exception is an instance of HttpException
        if ($this->isHttpException($e))
        {
            // Grab the HTTP status code from the Exception
            $status = $e->getStatusCode();
        }

        $response['status_code'] = $status;
        // Return a JSON response with the response array and status code
        return response()->json($response, $status);
    }
}
