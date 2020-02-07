<?php namespace Core\ProductSearch;

use Core\Http\Resource as Base;

class Resource extends Base
{
    protected $expandable = [
        //
    ];

    public function toArray($request)
    {   
        return $this->resource;
    }
}
