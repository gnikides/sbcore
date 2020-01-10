<?php namespace Core\Services;

class Shipping
{   
    public static function sizeUnits()
    {
        return [
            'mm' => 'mm',
            'cm' => 'cm',
            'm' => 'm',
            'in' => 'in',
            'ft' => 'ft',
            'yd' => 'yd'                                            
        ];    
    }
    
    public static function weightUnits()
    {
        return [
            'kg' => 'kg',
            'g' => 'g',
            'oz' => 'oz',
            'lb' => 'lb'                                            
        ];    
    }     
}
