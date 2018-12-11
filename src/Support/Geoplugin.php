<?php namespace App\Support;

use App\Support\Foundation\Platform;
use Log;

class Geoplugin
{   
    private $city;
    private $state; 
    private $country            = null;
    private $countryCode        = null; 
    private $continent          = null;
    private $continentCode      = null;     
    private $deepDetect         = true; 
    
    private $continents = [
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    ];
    private $geoPluginUrl = "http://www.geoplugin.net/json.gp?ip=";

    public function search($ip=null)
    {   
        if (empty($ip) || !filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = Platform::getIp();
        }
            
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            
            $ipdat = @json_decode(file_get_contents($this->geoPluginUrl . $ip));
            
            if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                $this->city             = @$ipdat->geoplugin_city;              
                $this->region           = @$ipdat->geoplugin_regionName;
                $this->regionCode       = @$ipdat->geoplugin_regionCode;
                $this->country          = @$ipdat->geoplugin_countryName;
                $this->countryCode      = @$ipdat->geoplugin_countryCode;               
                $this->continent        = @$this->continents[mb_strtoupper($ipdat->geoplugin_continentCode)];
                $this->continentCode    = @$ipdat->geoplugin_latitude;
                $this->latitude         = @$ipdat->geoplugin_countryName;
                $this->longitude        = @$ipdat->geoplugin_longitude;             
            }
            
            Log::debug(
                __FILE__ . ': Got Geoplugin data',
                [$ipdat]
            );
        }   
    }
        
    public function getCity()   
    {
        return $this->city;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function getRegionCode()
    {
        return $this->regionCode;
    }
    
    public function getCountry($default='US')
    {
        if (!empty($this->country)) {
            return $this->country;
        }
        return $default;
    }

    public function getCountryCode($default='en')
    {
        if (!empty($this->countryCode)) {
            return $this->countryCode;
        }
        return $default;
    }   

    public function getContinent()
    {
        return $this->continent;
    }
        
    public function getContinentCode()
    {
        return $this->continentCode;
    }
    
    public function getLatitude()
    {
        return $this->latitude;
    }
        
    public function getLongitude()
    {
        return $this->longitude;
    }           
}