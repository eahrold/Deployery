<?php

namespace App\Http\Middleware\Traits;

trait MiddlewareResponder
{

    /**
     * Does the request want JSON data back
     *
     * @return bool true for JSON false for Redirect
     */
    private function wantsJson()
    {
        return (request()->ajax() || request()->wantsJson());
    }

    /**
     * Do response or redirect
     *
     * @param  string $message Error message
     * @param  int    $code    Status Code For Response
     * @param  string $path    the URL to redirect to
     * @return mixed           The redirect or response
     */
    protected function redirectTo($message, $status_code, $path)
    {
        if ($this->wantsJson()) {
            return response()->json(compact('message', 'status_code'), $status_code);
        } else {
            return redirect($path);
        }
    }

    /**
     * Redirect to default path
     *
     * @param  string $message Error message
     * @param  int $code       Status Code For Response
     *
     * @return mixed           The redirect or response
     */
    protected function redirectToDefault(string $message, $code)
    {
        return $this->redirectTo($message, $code, '/');
    }

    /**
     * Redirect to the previous url
     *
     * @param  string $message Error message
     * @param  int $code       Status Code For Response
     * @return mixed           The redirect or response
     */
    protected function redirectToPrevious(string $message, $code)
    {
        return $this->redirectTo($message, $code, url()->previous());
    }

    /**
     * Redirect to the login page
     *
     * @param  string $message Error message
     * @param  int $code       Status Code For Response
     * @return mixed           Response
     */
    protected function redirectToLogin(string $message, $code)
    {
        return $this->redirectTo($message, $code, '/login');
    }
}
