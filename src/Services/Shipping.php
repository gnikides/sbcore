<?php namespace Core\Services;

class Shipping
{   
    const BY_WEIGHT         = 'by_weight';
    const BY_ITEMS          = 'by_items';
    const BY_SUBTOTAL       = 'by_subtotal';
    const FREE              = 'free';
    const ALLOWED_TYPES = [
        self::BY_WEIGHT,
        self::BY_ITEMS,
        self::BY_SUBTOTAL,
        self::FREE
    ];    
    const SPEED_1000 = 1000;
    const SPEED_2000 = 2000;    
    const SPEED_2500 = 2500;
    const SPEED_2600 = 2600;
    const SPEED_2800 = 2800;
    const SPEED_3000 = 3000;
    const SPEED_3500 = 3500;    
    const SPEED_4000 = 4000;  
    const SPEED_5000 = 5000;  
    const SPEED_6000 = 6000;  
    const SPEED_7000 = 7000;  
    const SPEED_8000 = 8000;  
    const SPEED_9000 = 9000;  
    const SPEED_9500 = 9500;  

    public static function speeds()
    {
        return [
            self::SPEED_1000 => __('Same day'),
            self::SPEED_2000 => __('Next day morning'),
            self::SPEED_2500 => __('Next day afternoon'),            
            self::SPEED_2600 => __('Next day'),            
            self::SPEED_2800 => __('Next day before end of day'),
            self::SPEED_3000 => __('1-2 days'),
            self::SPEED_3500 => __('1-3 days'),
            self::SPEED_4000 => __('2-4 days'),
            self::SPEED_5000 => __('4-8 days'),           
            self::SPEED_6000 => __('7-14 days'), 
            self::SPEED_7000 => __('2-4 weeks'), 
            self::SPEED_8000 => __('4-8 weeks'),            
            self::SPEED_9000 => __('60 days'),
            self::SPEED_9500 => __('90 days')                
        ];
    }

    public static function getSpeedLabel(int $speed)
    {
        return array_key_exists($speed, self::speeds()) ? self::speeds()[$speed] : '';
    } 

    public static function sizeUnits($system = 'metric')
    {   
        if ('imperial' == $system) {            
            return [
                'in' => 'in',
                'ft' => 'ft',
                'yd' => 'yd'                                            
            ];
        }
        return [
            'mm' => 'mm',
            'cm' => 'cm',
            'm' => 'm'                                     
        ];            
    }
    
    public static function weightUnits($system = 'metric')
    {
        if ('imperial' == $system) {            
            return [
                'oz' => 'oz',
                'lb' => 'lb'                                            
            ];
        }
        return [
            'kg' => 'kg',
            'g' => 'g',                                 
        ]; 
    }     
}
