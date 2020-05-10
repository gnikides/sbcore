<?php namespace Core\Services;

class Shipping
{   
    const FREE_SHIPPING     = 'free_shipping';
    const FIXED_RATE        = 'fixed_rate';  
    const FLEXIBLE_RATE     = 'flexible_rate';   
    const BY_WEIGHT_RULE    = 'by_weight';
    const PER_ITEM_RULE     = 'per_item';
    const PER_ORDER_RULE    = 'per_order';
    const ALLOWED_METHODS = [
        self::FREE_SHIPPING,
        self::FIXED_RATE,
        self::FLEXIBLE_RATE
    ];
    
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
