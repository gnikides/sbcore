<?php namespace Core\Support;

class Arr
{
    /**
     * Count non-empty elements or properties in an array or object; return false if only empty elements
     *
     * will count '0' as an element but false, null, '', 0 are not counted
     * 
     * @param  array or object $array
     * @return boolean         false if no elements    
    */        
    public static function countStrict(array $data = null)
    {
        //  let's count false and '0' but not null and not ''
        if ($data && ($data != '') && ($data !== null)) {           
            if (!is_array($data)) {
                return 1;            
            } else {
                $count = 0;                
                foreach ($data as $k => $v) {
                    $count += self::countStrict($v); 
                }
                return $count;
            }
        }
        return false;
    }
    
    /**
     * Parse options
     *
     * Can handle object, string or array of options
     *
     * @param  array $defaults
     * @param  array $options
     * @return array      
    */
    public static function parseOptions(array $defaults = [], array $options = [])
    {    
        $ay = [];
        if (is_object($options)) {
            $ay = get_object_vars($options);
        } elseif (is_array($options)) {
            $ay =& $options; 
        } elseif (is_string($options)) {
            //$options = urlencode($options);
            parse_str($options, $ay);
        }    
        if (is_array($defaults)) {
            $ay = array_merge($defaults, $ay);
        }
        return $ay;    
    }  

    /**
     * Shuffle assoc array, retaining keys
     *
     * @param array $array
     * @return array
    */    
    public static function shuffleAssoc(array $array)
    {  
        $keys = array_keys($array); 
        shuffle($keys);
        $rand = []; 
        foreach ($keys as $key) {
            $rand[$key] = $array[$key]; 
        }   
        return $rand;
    }                 
}