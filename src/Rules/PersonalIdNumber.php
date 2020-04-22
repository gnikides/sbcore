<?php namespace Core\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;

class PersonalIdNumber implements ImplicitRule
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
            $len = strlen($value);
            if ($len < 14 || $len > 15) {
                return false;
            }
        }
        return true;
    }

    public function message()
    {
        return $this->message ? $this->message : trans('Invalid field');
    }
}
