<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class DecodesJsonApi extends TransformsRequest
{
    /**
     * The attributes that should not be trimmed.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if(!is_string($value)) return $value;

        if (in_array($key, $this->except, true)) {
            return $value;
        }

        $result = json_decode($value);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $result;
        }
        return $value;
    }
}
