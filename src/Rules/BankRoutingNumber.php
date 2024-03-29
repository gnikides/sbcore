<?php namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class BankRoutingNumber implements Rule
{
    public $country_code;

    public function __construct($country_code)
    {
        $this->country_code = strtoupper($country_code);
    }

    public function passes($attribute, $value)
    {
        return true;
    }

    public function message()
    {
        return trans('Invalid field');
    }
}
