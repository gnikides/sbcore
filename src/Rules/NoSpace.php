<?php

namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoSpace implements Rule
{
    public function passes($attribute, $value)
    {
        // Trim leading and trailing spaces
        $value = trim($value);

        // Fail if there's any space
        if (strpos($value, ' ') !== false) {
            return false;
        }
        // If all checks pass, return true
        return true;
    }

    public function message()
    {
        return 'The string cannot contain spaces.';
    }
}
