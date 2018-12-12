<?php namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PersonalIdNumber implements Rule
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
            $len = strlen($value);
            if ($len < 14 || $len > 15) {
                return false;
            }
        }
        return true;
    }

    public function message()
    {
        return trans('The personal id number is invalid.');
    }


}
