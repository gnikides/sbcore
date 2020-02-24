<?php namespace Core\Http;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Core\Http\AnonymousResourceCollection;

class Resource extends JsonResource
{
    protected $expandable = [];
    protected $api_locale = 'default';
    protected $fallback_locale = 'default';
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    public function __construct($resource, $options = null)
    {   
        if (is_object($options)) {
            $this->api_locale = $options->getLocale();
        }            
        $this->resource = $resource;
    }

    public static function newCollection($resource, $options = null)
    {
        return tap(new AnonymousResourceCollection($resource, static::class, $options), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
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
    
    public function translate($values, $locale = '')
    {   
        if (is_string($values)) {
            return $values;
        }
        $locale = $locale ? $locale : $this->api_locale;
        $locale = $locale ? $locale : $this->fallback_locale;   
        return array_key_exists($locale, $values) ? $values[$locale] : null;
    }    
}