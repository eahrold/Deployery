<?php

namespace App\Services\Git\Validation;

use Illuminate\Contracts\Validation\Rule;

class ValidRepoBranch implements Rule
{
    private $url;
    private $pub_key;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($url, $pub_key=null)
    {
        $this->path = $path;
        $this->pub_key = $pub_key;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $info = new GitInfo($path);
        if (isset($this->pub_key)) {
            $info->withPubKey($pub_key);
        }
        return $info->hasBranch($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This is not a valid repo branch.';
    }
}
