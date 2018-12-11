<?php namespace App\Support\Validate;

class Postcode
{    
    public static function validate($postcode, $country_code)
    {    
        $country_code = strtoupper($country_code);
        $regex = [
            "US" => "^\d{5}([\-]?\d{4})?$",
            "UK" => "^(GIR|[A-Z]\d[A-Z\d]??|[A-Z]{2}\d[A-Z\d]??)[ ]??(\d[A-Z]{2})$",
            "DE" => "\b((?:0[1-46-9]\d{3})|(?:[1-357-9]\d{4})|(?:[4][0-24-9]\d{3})|(?:[6][013-9]\d{3}))\b",
            "CA" => "^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$",
            "FR" => "^(F-)?((2[A|B])|[0-9]{2})[0-9]{3}$",
            "IT" => "^(V-|I-)?[0-9]{5}$",
            "AU" => "^(0[289][0-9]{2})|([1345689][0-9]{3})|(2[0-8][0-9]{2})|(290[0-9])|(291[0-4])|(7[0-4][0-9]{2})|(7[8-9][0-9]{2})$",
            "NL" => "^[1-9][0-9]{3}\s?([a-zA-Z]{2})?$",
            "ES" => "^([1-9]{2}|[0-9][1-9]|[1-9][0-9])[0-9]{3}$",
            "DK" => "^([D-d][K-k])?( |-)?[1-9]{1}[0-9]{3}$",
            "SE" => "^(s-|S-){0,1}[0-9]{3}\s?[0-9]{2}$",
            "BE" => "^[1-9]{1}[0-9]{3}$"
        ];
 
        if ($regex[$country_code]) {
            if (!preg_match("/".$regex[$country_code]."/i", $postcode)){
                 return false;
             }
        }
        return true;    
    }  
}