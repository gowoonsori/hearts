<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class BadgeRule implements Rule{
    public function passes($attribute, $value){
    }

    public function message()
    {
        // TODO: Implement message() method.
    }
}
