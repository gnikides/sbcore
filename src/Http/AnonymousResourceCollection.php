<?php namespace Core\Http;

use Core\Http\ResourceCollection;

class AnonymousResourceCollection extends ResourceCollection
{
    public function __construct($resource, $collects, $request_locale = null)
    {
        $this->collects = $collects;
        $this->request_locale = $request_locale;
        parent::__construct($resource, $this->request_locale);
    }
}
