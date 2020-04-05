<?php namespace Core\ProductSearch;

use Core\Services\Elastic\BaseConfigurator;

class ScoutConfigurator extends BaseConfigurator
{
    public function __construct()
    {
        $this->name = config('scout.elasticsearch.index');
    }
}
