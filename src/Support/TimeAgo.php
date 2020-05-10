<?php namespace Core\Support;

use DateTime;
use DateTimeZone;

class TimeAgo
{
    public $timezone = 'UTC';

    public function __construct(string $timezone = '')
    {
        $this->timezone = $timezone ? $timezone : 'UTC';         
    }
    
    public function getPhrase($timestamp, string $timezone = '')
    {    
        $str = '';
        $elapsedTime = $this->getElapsedSeconds($timestamp, $timezone);
        if ($elapsedTime <= 10) {
            $str = __('just now');            
        } else {
            $periods = [
                86400   =>  __('day'),
                3600    =>  __('hour'),
                60      =>  __('min'),
                1       =>  __('sec')
            ];                                  
            foreach ($periods as $secs => $label) {
                $period = floor($elapsedTime / $secs);  
                if ($period > 1) {
                    $str = $period . ' ' . $label . 's';
                    break;
                } elseif ($period == 1) {
                    $str = $period . ' ' . $label;
                    break;              
                }
            }
        }
        return $str;        
    }

    public function getElapsedSeconds($timestamp, string $timezone = '')
    {    
        if (!$timezone) {
            $timezone = $this->timezone;
        }
        $tmz = new DateTimeZone($timezone);        
        $now = new DateTime('NOW', $tmz); 
        $then = new DateTime();
        $then->setTimestamp($timestamp);
        $then->setTimezone($tmz); 
        return $now->getTimestamp() - $then->getTimestamp();      
    }

    public function timeAgo($date, string $timezone = '')
    {
        if (!$timezone) {
            $timezone = $this->timezone;
        }    
        $timestamp = strtotime($date);
        
        //  timezone not important for huge time frames
        $now = time();

        $formatter = new DateTime($date, new DateTimeZone($timezone));                 
                
        if ($timestamp < $now-(365*86400)) {  //  1 year ago
            return $formatter->format('M j Y');
        } elseif ($timestamp < $now-(14*86400)) { //  two weeks ago
            return $formatter->format('M j');
        }
        return $this->getPhrase($timestamp, $timezone);   
    }         
}