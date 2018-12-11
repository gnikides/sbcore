<?php namespace Core\Validate;

class BusinessTaxId
{   
    public static function validate($id, $country_code)
    {   
        $country_code = strtoupper($country_code);
        
        if ('FR' == $country_code) {
            return \IsoCodes\Siren::validate($id);
        }   
        return true;
    }  
}