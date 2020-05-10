<?php namespace Core\Support;

class Arith
{               
    public static function getPercentage(int $val, int $total)
    {
        If (!empty($val) && !empty($total)) {
            return number_format(100 * ($val / $total), 0);
        }
        return false;
    }               			
}