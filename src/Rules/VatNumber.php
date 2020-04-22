<?php namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;
use IsoCodes\Vat;

class VatNumber implements Rule
{
    public $country_code;

    public function __construct($country_code)
    {
        $this->country_code = strtoupper($country_code);
    }

    public function passes($attribute, $value)
    {
        $value = preg_replace('/\s+/', '', $value);
        if (strcasecmp($this->country_code, substr($value, 0, 2)) != 0) {
            return false;
        }
        return Vat::validate($value);
    }

    public function message()
    {
        return trans('Invalid field');
    }
}
