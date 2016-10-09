<?php

namespace App\Models\Traits;

/**
 * Crypt/Decrypt Passwords using mutators
 *
 * @property password Password string
 **/
trait PasswordEncrypter {

    /**
     * Decrypt the password
     *
     * @param  string $value encrypted password
     * @return string        decrypted password
     */
    public function getPasswordAttribute($value = '')
    {
        try {
            return empty($value) ? $value : decrypt($value);
        } catch (DecryptException $e){
            \Log::debug("Could not decrypt the Password: {$e->getMessage()}");
        }
        return '';
    }

    /**
     * Encrypt the password
     *
     * @param  string $value unencrypted password
     */
    public function setPasswordAttribute($value = '')
    {
        // Don't encrypt the password twice.
        if ($value === $this->attributes['password']) {
            return;
        }
        $this->attributes['password'] = empty($value) ? $value : encrypt($value);
    }

}