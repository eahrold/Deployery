<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Hash;

/**
 * Bcrypts passwords from the request on save.
 */
trait BcryptsPassword {

    public function setRawBcryptedPassword($value) {
        $this->attributes['password'] = $value;
        return $this;
    }

    protected function setPasswordAttribute($value)
    {
        if(Hash::needsRehash($value)){
            $this->attributes['password'] = Hash::make($value);;
        }
        return $this;
    }
}