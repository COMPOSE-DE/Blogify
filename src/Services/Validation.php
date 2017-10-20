<?php

namespace ComposeDe\Blogify\Services;

use BlogifyAuth;
use Hash;
use Illuminate\Validation\Validator;

class Validation extends Validator
{

    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateAuthUserPass($attribute, $value, $parameters)
    {
        $passCheck = Hash::check(
            $value,
            BlogifyAuth::user()->getAuthPassword()
        );

        if (!$passCheck) {
            return false;
        }

        return true;
    }

}
