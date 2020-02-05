<?php namespace Core\Http;

use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    protected $expandable = [];
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
        return array_get($this->data, $key, null);
    }    
}