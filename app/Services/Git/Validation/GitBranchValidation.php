<?php
namespace App\Services\Git\Validation;

use App\Services\Git\GitInfo;
use Illuminate\Validation\Validator;

class GitBranchValidation
{

    public function validate($attribute, $value, $parameters, Validator $validator)
    {
        $path = $parameters[0];
        $info = new GitInfo($path);
        if (isset($parameters[1])) {
            $info->withPubKey($parameters[1]);
        }
        return $info->hasBranch($value);
    }

    public function replace($message, $attribute, $rule, $parameters)
    {
        return "The git branch is not valid";
    }

}