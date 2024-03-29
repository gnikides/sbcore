<?php

use Symfony\Component\VarDumper\VarDumper;
use Carbon\Carbon;
use Core\Support\TimeAgo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\File;

//  print_r a variable
if (!function_exists('pr')) { 
    function pr($var, bool $die = false)
    { 
        echo '<pre>';
        echo gettype($var) . ": ";
        print_r($var);
        echo '</pre>';
        if ($die) {
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

if (!function_exists('sanitizeStrings')) {
    function sanitizeStrings($value = '')
    {
        $output = [];
        if ($value && is_array($value)) {
            foreach ($value as $k => $v) {
                $output[strtolower(sanitizeString($k))] = sanitizeString($v);
            }
        }
        return $output;      
    }
}

if (!function_exists('sanitizeInts')) {
    function sanitizeInts($value)
    {
        $output = [];
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $output[strtolower(sanitizeString($k))] = sanitizeInt($v);
            }
        }
        return $output;      
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

if (!function_exists('getMorphedModel')) {  
    function getMorphedModel(string $slug)
    {   
        $relations = Relation::morphMap();
        if ($relations) {
            return $relations[$slug];
        }
        return false;
    }
}

if (!function_exists('isJson')) {  
    function isJson($value) {
        if (!is_string($value)) {
            return false;
        }    
        json_decode($value);
        return (json_last_error() == JSON_ERROR_NONE);
   }
}

function sortByValue($array, $field, $dir = SORT_ASC)
{
    $sort = [];
    foreach ($array as $key => $val) {
        $sort[$key] = $val[$field];
    }
    array_multisort($sort, $dir, $array);
    return $array;
} 

if (!function_exists('countDirectories')) {
    function countDirectories($dir)
    {
        $allDirectories = File::directories($dir);
        $count = 0;

        foreach ($allDirectories as $dir) {
            $dirname = basename($dir);

            // Skip dot files and .DS_Store files
            if ($dirname[0] === '.'
                || $dirname[0] === '..'
                || $dirname === '.DS_Store') {
                continue;
            }
            $count++;
        }
        return $count;
    }
}

if (!function_exists('countFiles')) {
    function countFiles($dir)
    {
        $files = File::allFiles($dir);
        $count = 0;

        foreach ($files as $file) {
            $filename = $file->getFilename();

            // Skip dot files and .DS_Store files
            if ($filename[0] !== '.'
                && $filename[0] !== '..'
                && $filename !== '.DS_Store') {
                $count++;
            }
        }
        return $count;
    }
}
