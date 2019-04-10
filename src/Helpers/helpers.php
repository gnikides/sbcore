<?php

use Illuminate\Support\Debug\Dumper;
use Carbon\Carbon;
use Core\Support\TimeAgo;

//  dump a variable
if (!function_exists('sb')) {  
    function sb($var, bool $die = false)
    { 
        array_map(function($var) {
            echo gettype($var) . ": ";
            (new Dumper)->dump($var);
        }, func_get_args());
        if ($die == true) {
            die();
        }
    }
}

//  print_r a variable
if (!function_exists('pr')) { 
    function pr($var, bool $die = false)
    { 
        echo '<pre>';
        echo gettype($var) . ": ";
        print_r($var);
        echo '</pre>';
        if ($die == true) {
            die();
        }
    }
}

//  utility log function
if (!function_exists('lg')) { 
    function lg($array, string $message = 'debug')
    {   
        if (!is_array($array)) {
            $array = [$array];
        }
        \Log::info($message, $array);
    }
}

// Format time for mysql insert
if (!function_exists('now')) {
    function now($timezone = 'UTC')
    { 
        return Carbon::now($timezone);
    }
}

// truncate text
if (!function_exists('truncate')) {
    function truncate(string $text, int $length = 400, string $break_at = ' ', string $message = null)
    {
        $min_length = 10;        
        if (strlen($text) < $length) {     
            return $text;         
        } else {            
            //   add a bit of text so we can find "space"
            $truncated = substr($text, 0, ($length + 3));
            $pos = strrpos($truncated, $break_at);        
            if ($pos !== false) {
                $truncated = trim(substr($truncated, 0, $pos));                
                //  ensure not too short
                if (strlen($truncated) < $min_length) {
                    $truncated = substr($text, 0, $length);
                }
            }
            if ($message) {
                $truncated .= ' ' . $message;
            }
            return $truncated;
        }
        return false;
    }
}

if (!function_exists('sanitizeString')) {
    function sanitizeString($value)
    {
        return filter_var($value, FILTER_SANITIZE_STRING);
    }
 }

if (!function_exists('sanitizeInt')) {
    function sanitizeInt($value)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    } 
}

if (!function_exists('sanitizeFloat')) {
    function sanitizeFloat($value)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
    } 
}

if (!function_exists('timeAgo')) {
    function timeAgo($date)
    {
        $time = new TimeAgo(config('app.timezone'));
        return $time->timeAgo($date);
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, string $format = 'M j, Y h:i', $timezone = 'UTC')
    {
        return (new DateTime($date, new DateTimeZone($timezone)))->format($format);
    }
}

if (!function_exists('safeCount')) {  
    function safeCount($var = null)
    {   
        //  as object must implement Countable but expensive to do that test
        return is_array($var) || is_object($var)  ? @count($var) : 0;
    }
}

if (!function_exists('emptyToNull')) {  
    function emptyToNull($key, array $input = [])
    {   
        return !array_key_exists($key, $input) || empty($input[$key]) ? null : $input[$key];
    }
}

