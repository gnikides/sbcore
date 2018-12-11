<?php namespace Story;

use DateTime;
use DateTimeZone;

class TimeTool
{
    public $timezone = 'UTC';

    public function __construct($timezone='')
    {
        if (!empty($timezone)) {
            $this->timezone = $timezone;
        }           
    }   

    public function format($time, $format="r", $timezone='')
    {       
        if (empty($format)) {
            $format = $this->defaultFormat;
        }
        if (empty($timezone)) {
            $timezone = $this->timezone;
        }
        $Date = new DateTime($time, new DateTimeZone($timezone));                 
        return $Date->format($format);
    }

    public function formatTimestamp($time, $format="r", $timezone='')
    {       
        if (empty($format)) {
            $format = $this->defaultFormat;
        }
        if (empty($timezone)) {
            $timezone = $this->timezone;
        }
        $Date = new DateTime();
        $Date->setTimestamp($time);
        $Date->setTimezone(new DateTimeZone($timezone));                 
        
        return $Date->format($format);
    }

    public function formatForMySQL($timestamp, $timezone='')
    {       
        $Date = new DateTime();
        $Date->setTimestamp($timestamp);
        $Date->setTimezone(new DateTimeZone('UTC')); 
        return $Date->format("Y-m-d H:i:s");
    }
    
    public function getTimeAgoTimestamp($timestamp, $timezone='')
    {    
        $str = '';

        $elapsedTime = $this->getElapsedSeconds($timestamp, $timezone);

        if ($elapsedTime <= 5) {
            $str = Story::__('just now');
            
        } else {
            $periods = array(
                86400   =>  'day',
                3600    =>  'hour',
                60      =>  'min',
                5       =>  'sec'
            );                                  
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

    public function getTimeAgo($date, $timezone='')
    {    
        return $this->getTimeAgoTimestamp(strtotime($date), $timezone);
    }

    public function getElapsedSecondsFromDatetime($datetime, $timezone='')
    {   
        if (empty($timezone)) {
            $timezone = $this->timezone;
        }     
        $timestamp = strtotime($datetime);
        return $this->getElapsedSeconds($timestamp, $timezone);      
    }

    public function getElapsedSeconds($timestamp, $timezone='')
    {    
        if (empty($timezone)) {
            $timezone = $this->timezone;
        }
        $Now = new DateTime('NOW', new DateTimeZone($timezone)); 

        $Then = new DateTime();
        $Then->setTimestamp($timestamp);
        $Then->setTimezone(new DateTimeZone($timezone)); 

        return $Now->getTimestamp() - $Then->getTimestamp();      
    }

    public function formatReadable($date)
    {
        $timestamp          = strtotime($date);

        if ($timestamp < time()-(365*86400)) {  //  1 year ago
            return $this->format($date, 'M j Y');

        } elseif ($timestamp < time()-(14*86400)) { //  two weeks ago
            return $this->format($date, 'M j');

        } else {
            return $this->getTimeAgo($date);
        }   
    }         
}