<?php

namespace App\Validation\Rules;

use App\Models\Users;

use \Respect\Validation\Rules\AbstractRule;

/**
 * Class LoginAvailable - check is login unique
 *
 * @package App\Validation\Rules
 */
class LoginAvailable extends AbstractRule
{
    public function validate($input)
    {
        return Users::where("login",$input)->count()===0;
    }
}