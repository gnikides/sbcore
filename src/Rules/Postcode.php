<?php namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Postcode implements Rule
{
    public $country_code;
    protected $countries_in_postcode_db = [];

    public function __construct($country_code)
    {
        $this->country_code = strtoupper($country_code);
    }

    public function passes($attribute, $value)
    {
        $value = preg_replace('/\s+/', '', $value);
        return $this->validate($value);
    }

    public function message()
    {
        return trans('The postcode is invalid.');
    }

    public function validate($postcode)
    {
        $regex = [
            'US' => "^\d{5}([\-]?\d{4})?$",
            'UK' => "^(GIR|[A-Z]\d[A-Z\d]??|[A-Z]{2}\d[A-Z\d]??)[ ]??(\d[A-Z]{2})$",
            'DE' => "\b((?:0[1-46-9]\d{3})|(?:[1-357-9]\d{4})|(?:[4][0-24-9]\d{3})|(?:[6][013-9]\d{3}))\b",
            'CA' => "^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$",
            'FR' => "^(F-)?((2[A|B])|[0-9]{2})[0-9]{3}$",
            'IT' => "^(V-|I-)?[0-9]{5}$",
            'AU' => "^(0[289][0-9]{2})|([1345689][0-9]{3})|(2[0-8][0-9]{2})|(290[0-9])|(291[0-4])|(7[0-4][0-9]{2})|(7[8-9][0-9]{2})$",
            'NL' => "^[1-9][0-9]{3}\s?([a-zA-Z]{2})?$",
            'ES' => "^([1-9]{2}|[0-9][1-9]|[1-9][0-9])[0-9]{3}$",
            'DK' => "^([D-d][K-k])?( |-)?[1-9]{1}[0-9]{3}$",
            'SE' => "^(s-|S-){0,1}[0-9]{3}\s?[0-9]{2}$",
            'BE' => "^[1-9]{1}[0-9]{3}$"
        ];
        if ($regex[$this->country_code] && !preg_match("/".$regex[$this->country_code]."/i", $postcode)) {
            return false;
        }
        if (in_array($this->country_code, $this->countries_in_postcode_db) && !$this->checkAgainstDatabase($postcode)) {
            return false;
        }
        return true;
    }
    
    public static function checkAgainstDatabase($postcode)
    {
        if ('CA' == $this->country_code) {
            $postcode = substr($postcode, 0, 3);
        }
        // $result = app(PostcodeRepo::class)->firstBy([
        //     'postcode'      => $postcode,
        //     'country_code'  => $country_code
        // ]);  
        // if (@$result['id']) {
            return true;
 //       }
        return false;
    }
}
