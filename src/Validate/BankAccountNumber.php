<?php namespace Core\Validate;

class BankAccountNumber
{   
    public static function validate($number, $country_code)
    {           
        $iban_countries = [
            'AL',
            'AD',
            'AT',
            'BE',
            'BA',
            'BG',
            'HR',
            'CY',
            'CZ',
            'DK',
            'EE',
            'FO',
            'FI',
            'FR',
            'PF', // French Polynesia
            'TF', // French Southern Territories
            'GP', // French Guadeloupe
            'MQ', // French Martinique
            'YT', // French Mayotte
            'NC', // New Caledonia
            'RE', // French Reunion
            'BL', // French Saint Barthelemy
            'MF', // French Saint Martin
            'PM', // Saint Pierre et Miquelon
            'WF', // Wallis and Futuna Islands
            'GE',
            'DE',
            'GI',
            'GR',
            'GL',
            'HU',
            'IS',
            'IE',
            'IL',
            'IT',
            'KZ',
            'KW',
            'LV',
            'LB',
            'LI',
            'LT',
            'LU',
            'MK',
            'MT',
            'MR',
            'MU',
            'MC',
            'ME',
            'NL',
            'NO',
            'PL',
            'PT',
            'RO',
            'SM',
            'SA',
            'RS',
            'SK',
            'SI',
            'ES',
            'SE',
            'CH',
            'TN',
            'TR',
            'AE',
            'GB',
            'CI',
        ];
        
        $country_code = strtoupper($country_code);
        
        if (in_array($country_code, $iban_countries)) {
            return \IsoCodes\Iban::validate($number);
        }   
        return true;
    }  
}