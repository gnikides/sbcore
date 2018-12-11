<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use IsoCodes\Siren;

class BusinessTaxId implements Rule
{
    public $country_code;

    public function __construct($country_code)
    {
        $this->country_code = strtoupper($country_code);
    }

    public function passes($attribute, $value)
    {
        $value = preg_replace('/\s+/', '', $value);
        if ('FR' == $this->country_code) {
            return Siren::validate($value);
        }
        return true;
    }

    public function message()
    {
        return trans('The business tax id is invalid.');
    }
}
