<?php namespace Story;

class Arithmetic
{              
    /**
     * Get percentage of value from total
     *
     * @param   int     $val
     * @param   int     $total    
     * @return  mixed   int on success, false on failure
    */    
    public static function getPercentage($val, $total)
    {
        If (!empty($val) && !empty($total)) {
            return number_format(100 * ($val / $total), 0);
        }
        return false;
    }               			
}