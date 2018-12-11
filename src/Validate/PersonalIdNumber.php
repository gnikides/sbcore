<?php namespace App\Support\Validate;

class PersonalIdNumber
{    
    public static function validate($id_number, $country_code)
    {    
        $country_code = strtoupper($country_code);
        if ('FR' == $country_code) {
            $len = strlen($id_number);
            if ($len < 14 || $len > 15) {
                return false;
            }
        }
        return true;    
    }  
}