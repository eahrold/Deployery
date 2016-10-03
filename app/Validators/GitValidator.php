<?php

namespace App\Validators;

use App\Services\GitInfo;
use Illuminate\Validation\Validator as IlluminateValidator;

class GitValidator extends IlluminateValidator {

    protected $messages = [
        "git_branch" => "This is not a valid git :attribute",
    ];

    public function __construct( $translator, $data, $rules, $messages = [], $customAttributes = [] ) {
        parent::__construct( $translator, $data, $rules, $messages, $customAttributes );
        $this->set_messages();
    }

    /**
     * Setup any customizations etc
     *
     * @return void
     */
    protected function set_messages() {
        //setup our custom error messages
        $this->setCustomMessages( $this->messages );
    }

    /**
     * Check if the branch is valid
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    protected function validateGitBranch( $attribute, $value, $parameters ) {
        $path = $parameters[0];
        $info = new GitInfo($path);
        if(isset($parameters[1])){
            $info->withPubKey($parameters[1]);
        }
        return $info->hasBranch($value);
    }
}
