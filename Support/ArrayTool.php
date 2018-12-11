<?php namespace Story;

class ArrayTool
{
    /**
     * Count non-empty elements or properties in an array or object; return false if only empty elements
     *
     * will count '0' as an element but false, null, '', 0 are not counted
     * 
     * @param    array or object        $array
     * @return    boolean             false if no elements    
    */        
    public static function countStrict($array)
    {
        //  let's count false and '0' but not null and not ''
        if (isset($array) && ($array != '') && ($array !== null)) {
           
            if (!is_array($array)) {
                return 1;
            
            } else {
                $count = 0;
                
                foreach ($array as $k => $v) {
                    $count += self::countStrict($v); 
                }
                return $count;
            }
        }
        return false;
    }

    public static function cache($array, $file, $name='')
    {   
        $str = var_export($array, true);
        if (empty($name)) {
        	$str = 'return ' . $str;  
        } else {	
        	$str = $name . '=' . $str;
        }
        if (File::make("<?php\n$str;", $file)) {
            return true;
        }
        return false;
    }

	public static function incrementKeyIfExists($array, $key)
	{
		if (isset($array[$key])) {
			$key++;
			return $key;
		}
		return $key;
	}
	    
    public static function dump($array)
    {
        $str = 'SIZE=' . (count($array))
        . '<br />'
        . var_dump($array, true); 
        echo $str;
    } 
    
    //	ensure an array is first in an array of arguments, conserving nulls
    public static function unshiftArgs($args=null, $firstArgs=null)
    {  
    	if ((null == $args) && (null == $firstArgs)) {
    		return null;
    	}	
    	if (null == $args) {
			if (null != $firstArgs) {
				$args = array($firstArgs);
			}	
		} else {
			if (!is_array($args)) {
				$args = array($args);
			}
			if (null != $firstArgs) {		
				array_unshift($args, $firstArgs);
			}	
		}
		return $args;
	}
	
    public static function sortOnConfig($data, $sortKey='sort_order', $direction=SORT_ASC)
    {
        $order = array(); 		  
		foreach ($data as $k => $v) {
			$order[$k] = $sortKey;
		}
		if (empty($direction)) {
			$direction = SORT_ASC;
		}
		array_multisort($order, $direction, $data);
		return $data;	
    }
    
    /**
     * Parse options
     *
     * Can handle object, string or array of options
     *
     * @param    array    $defaults
     * @param    array    $options
     * @return    boolean         
    */
    public static function parseOptions($defaults=array(), $options=array())
    {    
        $ay = array();
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
    
    public static function seekInMulti($array, $keys=array())
    {	
        if (isset($keys[4]) && isset($array[$keys[0]][$keys[1]][$keys[2]][$keys[3]][$keys[4]])) {
       		return $array[$keys[0]][$keys[1]][$keys[2]][$keys[3]][$keys[4]];         
        
        } elseif (isset($keys[3]) && isset($array[$keys[0]][$keys[1]][$keys[2]][$keys[3]])) {
       		return $array[$keys[0]][$keys[1]][$keys[2]][$keys[3]];         
        
        } elseif (isset($keys[2]) && isset($array[$keys[0]][$keys[1]][$keys[2]])) {
       		return $array[$keys[0]][$keys[1]][$keys[2]]; 
        
        } elseif (isset($keys[1]) && isset($array[$keys[0]][$keys[1]])) {
       		return $array[$keys[0]][$keys[1]];
       	
       	} elseif (isset($array[$keys[0]])) {          
            return $array[$keys[0]];
        }
    } 
    
    public static function shuffleAssoc($array)
    { 
		if (is_array($array)) { 
			$keys = array_keys($array); 
			shuffle($keys);
			$rand = array(); 
			foreach ($keys as $key) {
				$rand[$key] = $array[$key]; 
			}	
			return $rand;
		}
		return false;	 
	}         	      
}