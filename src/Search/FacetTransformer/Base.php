<?php namespace Core\Search\FacetTransformer;

abstract class Base
{
    public function __construct($collection = null)
    {
        $this->collection = $collection;
    }
}
