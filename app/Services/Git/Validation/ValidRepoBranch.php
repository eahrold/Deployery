<?php

namespace App\Services\Git\Validation;

use App\Services\Git\GitInfo;
use Illuminate\Contracts\Validation\Rule;

class ValidRepoBranch implements Rule
{
    private $path;
    private $pub_key;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($path, $pub_key=null)
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
        $info = new GitInfo($this->path);
        if (isset($this->pub_key)) {
            $info->withPubKey($this->pub_key);
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
