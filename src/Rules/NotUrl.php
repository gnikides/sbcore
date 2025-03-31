<?php

namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class NotUrl implements Rule
{
    public function passes($attribute, $value)
    {
        // Regex to check if the text contains a URL
        return !preg_match('/https?:\/\/[^\s]+/', $value);
    }

    public function message()
    {
        return 'The text cannot contain a URL';
    }
}
