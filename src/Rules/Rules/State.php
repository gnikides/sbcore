<?php

namespace App\Support\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Country;

class State implements Rule
{
    public $country_code;

    public function __construct()
    {
        $this->country_code = isset($country_code) ? strtoupper($country_code) : '';
    }

    public function passes($attribute, $value)
    {
        return $this->validate(preg_replace('/\s+/', '', $value));
    }

    public function message()
    {
        return trans('The state is invalid.');
    }

    public function validate($postcode)
    {   
        if ($this->country_code) {      
            $country = new Country(api('country')->get($this->country_code));
            if ($country->isStateRequired() && empty($state)) {  
                return false;
            }
        }    
        return true;
    }
}
