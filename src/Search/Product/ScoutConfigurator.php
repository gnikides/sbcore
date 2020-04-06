<?php namespace Core\Search\Product;

use Core\Services\Elastic\BaseConfigurator;

class ScoutConfigurator extends BaseConfigurator
{
    public function __construct()
    {
        $this->name = config('scout.elasticsearch.index');
    }
}
