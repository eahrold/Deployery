<?php
namespace App\Services\Git\Validation;

use App\Services\Git\GitInfo;
use Illuminate\Validation\Validator;

class GitCloneValidation
{

    public function validate($attribute, $value, $parameters, Validator $validator)
    {
        $pattern = '/^(?:git|ssh|https?|git@[-\w.]+):(\/\/)?(.*?)(\.git)(\/?|\#[-\d\w._]+?)$/';
        return boolval(preg_match($pattern, $value));
    }

    public function replace($message, $attribute, $rule, $parameters)
    {
        return "The git clone url is not valid";
    }

}