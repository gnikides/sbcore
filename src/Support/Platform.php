<?php namespace Core\Support;

class Platform
{
    private static $bots = [
        'bot',
        'crawl',
        'spider',
        'slurp',
        'acoon',
        'ah-ha',
        'appie',
        'arach',
        'atomz',
        'deepindex',
        'ezresult',
        'robot',
        'KIT-Fireball',
        'MantraAgent',
        'nazilla',
        'winona',
        'whatuseek',
        'zyborg'
    ];
    
    public static function getIp()
    {   
        if ('local' == config('app.env')) {
            return false;
        }   
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) 
            && filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])
            && filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {                    
            return $_SERVER["HTTP_CLIENT_IP"];

        } elseif (isset($_SERVER['REMOTE_ADDR'])
            && filter_var(@$_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {                   
            return $_SERVER["REMOTE_ADDR"];
        
        } elseif (getenv('HTTP_X_FORWARDED_FOR')
            && filter_var(@getenv('HTTP_X_FORWARDED_FOR'), FILTER_VALIDATE_IP)) {    
            return getenv('HTTP_X_FORWARDED_FOR');
        
        } elseif (getenv('HTTP_CLIENT_IP')
            && filter_var(@getenv('HTTP_CLIENT_IP'), FILTER_VALIDATE_IP)) {     
            return getenv('HTTP_CLIENT_IP');

        } elseif (getenv('REMOTE_ADDR') 
            && filter_var(@getenv('REMOTE_ADDR'), FILTER_VALIDATE_IP)) {                   
            return getenv('REMOTE_ADDR');
        }            
        return false;
    }

    public static function getBrowserLanguage($default='en')
    {   
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        
            $accepts = explode(",", $accept);
            foreach ($accepts as $val) {
                #check for q-value and create associative array. No q-value means 1 by rule
                if (preg_match("/(.*);q=([0-1]{0,1}.d{0,4})/i", $val, $matches)) {
                    $lang[$matches[1]] = (float)$matches[2];
                } else {
                    $lang[$val] = 1.0;
                }   
            }

            #return default language (highest q-value)
            $qval = 0.0;
            foreach ($lang as $key => $value) {
                if ($value > $qval) {
                    $qval = (float)$value;
                    $default = $key;
                }
            }
        }
        return strtolower($default);
    }
                 
    public static function makeBrowserSignature()
    {
        $keys = [
            'HTTP_USER_AGENT',
            'SERVER_PROTOCOL', 
            'HTTP_ACCEPT_CHARSET',
            'HTTP_ACCEPT_ENCODING',
            'HTTP_ACCEPT_LANGUAGE',
            'SERVER_ADDR'
        ]; 
        $str = ''; 
        foreach ($keys as $v) { 
            if (isset($_SERVER[$v])) {
                $str .= $_SERVER[$v];
            }    
        }
        return md5($str);
    } 

    public static function isBot()
    {          
        foreach (self::$bots as $bot) {                
            if (stripos(getenv('HTTP_USER_AGENT'), $bot) !== false) {    
                return true;
            }
        }
        return false;
    }

    public static function isCli()
    {
        return (php_sapi_name() == 'cli');  
    }            
}