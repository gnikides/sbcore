<?php namespace Core\Http;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class Resource extends JsonResource
{
    protected $expandable = [];
    protected $api_locale = [];
    protected $api_fallback_locale = [];
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    public function toArray($request)
    {
        return parent::toArray($request);
    }

    public function expands($node, $value, $relation = '')
    {
        if (!$relation) {
            $relation = $node;
        }
        return $this->when($this->isExpandable($node) && is_object($this->{$relation}), $value);
    }

    public function notExpands($node, $value, $relation = '')
    {
        if (!$relation) {
            $relation = $node;
        }        
        return $this->when(!$this->isExpandable($node) || !is_object($this->{$relation}), $value);
    }
    
    public function isExpandable($endpoint)
    {
        return in_array($endpoint, $this->expandable); 
    } 

    public function setExpandable($endpoints = [])
    {
        $this->expandable = $endpoints;
        return $this;
    }

    public function noExpands()
    {
        $this->expandable = [];
        return $this;
    }

    public function setData($key, $value)
    { 
        $this->data[$key] = $value;
        return $this;
    }

    public function getData($key)
    {
        return Arr::get($this->data, $key, null);
    }  
    
    public function resolveTranslation($values)
    {   
        if (is_string($values)) {
            return $values;
        } elseif ($this->api_locale && array_key_exists($this->api_locale, $values)) {
            $value = $values[$this->api_locale];
        } elseif ($this->api_fallback_locale && array_key_exists($this->api_fallback_locale, $values)) {
            $value = $values[$this->api_fallback_locale];
        } else {
            $value = array_values($values)[0];
        }
        return $value;
    }    
}