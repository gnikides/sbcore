<?php namespace Core\Search\Product;

use Core\Search\BaseConfigurator;

class ScoutConfigurator extends BaseConfigurator
{
    public function __construct()
    {
        $this->name = config('scout.elasticsearch.index');
    }
}
