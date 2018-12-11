<?php namespace Story;

class Serializer
{
    public static function isSerialized($data)
    {
        if ((trim($data) == "") || !is_string($data)) {
            return false;
        }         
        if (preg_match("/^(i|s|a|o|d)(.*);/si", $data)) {
            return true; 
        } 
        return false; 
    }
        
    /**
     * Unserialize only data that has been serialized
    */        
    public static function unserializeSerialized($data)
    {
        if (self::isSerialized($data)) {
            $data = trim($data);
            if (false !== $var = @unserialize($data)) {
                return $var;
            }
        }    
        return false;
    }

    /**
     * Unserialize into an array data that has been serialized
    */        
    public static function getArray($data)
    {
        if (is_array($data)) {
            return $data;
        } elseif ($ay = self::unserializeSerialized($data)) {
            if (is_array($ay)) {
                return $ay;
            }    
        }
        return false;
    }

    /**
     * Unserialize into an object data that has been serialized
    */        
    public static function getObject($data)
    {
        if (is_object($data)) {
            return $data;
        } elseif ($obj = self::unserializeSerialized($data)) {
            if (is_object($obj)) {
                return $obj;
            }    
        }
        return false;
    } 
     
	public static function removeSpecialChars($str)
	{
		return preg_replace('/[^a-z0-9]+/', '', serialize($str));  
	}	
	   
}
