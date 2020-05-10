<?php namespace Core\Support;

use Jenssegers\Model\Model as Base;
use Illuminate\Support\Arr;

class Model extends Base
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';    
    const SAVED = 'saved';    

    public function getObjectAttribute()
    {
        return $this->attributes['object'];
    }

    public function getProp($object, string $name, $field = null)
    {
        if (is_array($object->props)) {
            $prop = Arr::get($object->props, $name);
            if ($prop && $field) {
                return Arr::get($prop, $field);
            }
            return $prop;
        }
        return false;    
    }    
}