<?php namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class Over18Years implements Rule
{
    public function __construct()
    {
    }

    public function passes($attribute, $value)
    {
        return $value > (date('Y') - 100) && $value < (date('Y') - 17);
    }

    public function message()
    {
        return trans('The month is invalid.');
    }
}
