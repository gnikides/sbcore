<?php namespace Core\Http;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Core\Http\AnonymousResourceCollection;

class Resource extends JsonResource
{
    protected $data = [];
    protected $expandable = [];
    protected $request_locale = 'default';
    protected $fallback_locale = 'default';
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    public function __construct($resource, $request_locale = 'default')
    {   
        $this->request_locale = $request_locale ? $request_locale : 'default';            
        $this->resource = $resource;
    }

    public static function collection($resource, $request_locale = null)
    {   
        return tap(new AnonymousResourceCollection($resource, static::class, $request_locale ), function ($collection) {
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

    public function setRequestLocale($locale)
    { 
        $this->request_locale = $locale;
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
}
