<?php

namespace App\Services\Git\Traits;

trait GitAuthenticatable {

    protected $pub_key;
    public function withPubKey($pub_key=null) {
        $this->pub_key = $pub_key;
        return $this;
    }


    protected $password;
    public function withPassword($password=null){
        $this->password = $password;
        return $this;
    }
}