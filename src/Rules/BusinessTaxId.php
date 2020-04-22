<?php namespace Core\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;
use IsoCodes\Siren;

class BusinessTaxId implements ImplicitRule
{
    public $country_code;
    public $message;

    public function __construct($country_code)
    {
        $this->country_code = strtoupper($country_code);
    }

    public function passes($attribute, $value)
    {
        $value = preg_replace('/\s+/', '', $value);
        if ('FR' == $this->country_code) {
            if (empty($value)) {
                $this->message = trans('Required field');
                return false;
            }
            return Siren::validate($value);
        }
        return true;
    }

    public function message()
    {
        return $this->message ? $this->message : trans('Invalid field');
    }
}
