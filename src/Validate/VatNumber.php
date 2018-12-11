<?php namespace Core\Validate;

class VatNumber
{   
    public static function validate($vat, $country_code)
    {   
        if (strcasecmp($country_code, substr($vat, 0, 2)) != 0) {
            return false;
        }   
        return \IsoCodes\Vat::validate($vat);
    }  
}