<?php namespace Core\Services\Stripe;

class Helper
{ 
    public static function formatParams(string $input_key, string $output_key, array $input)
    {     
        $output = [];
        if (array_key_exists($input_key, $input) && $input[$input_key]) {
            $val = explode('.',$output_key);
            if (isset($val[2])) {
                $output[$val[0]][$val[1]][$val[2]] = $input[$input_key];
            } elseif (isset($val[1])) {    
                $output[$val[0]][$val[1]] = $input[$input_key];
            } elseif (isset($val[0])) {       
                $output[$val[0]] = $input[$input_key];
            }    
        }
        return $output;
    }
}    