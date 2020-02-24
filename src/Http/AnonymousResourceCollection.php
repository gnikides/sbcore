<?php namespace Core\Http;

//use Illuminate\Http\Resources\Json\AnonymousResourceCollection as Base;
use Core\Http\ResourceCollection;

class AnonymousResourceCollection extends ResourceCollection
{
    public function __construct($resource, $collects, $options)
    {
        $this->collects = $collects;

        parent::__construct($resource, $options);
    }
}
