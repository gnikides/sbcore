<?php namespace Core\Services;

class Probability
{
    /*
        PHP: Get all combinations of multiple arrays (preserves keys)
        https://gist.github.com/cecilemuller/4688876
        Thanks Cecile Muller
    */    
    public static function combinations($arrays)
    {
        $result = array(array());
        foreach ($arrays as $property => $property_values) {
            $tmp = array();
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, array($property => $property_value));
                }
            }
            $result = $tmp;
        }
        return $result;
    }
}
