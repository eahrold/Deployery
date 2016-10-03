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
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        // If the request wants JSON (AJAX doesn't always want JSON)
        if ($request->is('api/*'))
        {
            // Define the response
            $response = [
                'errors' => 'Sorry, something went wrong.',
                'message' => $e->getMessage()
            ];

            if (config('app.debug'))
            {
                $response['exception'] = get_class($e);
                $response['trace'] = $e->getTrace();
            }

            // Default response of 400
            $status = 400;

            // If this exception is an instance of HttpException
            if ($this->isHttpException($e))
            {
                // Grab the HTTP status code from the Exception
                $status = $e->getStatusCode();
            }

        // Return a JSON response with the response array and status code
        return response()->json($response, $status);
    }

        return parent::render($request, $e);
    }
}
