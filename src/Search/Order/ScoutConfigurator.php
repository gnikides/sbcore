<?php namespace Core\Search\Order;

use Core\Services\Elastic\BaseConfigurator;

class ScoutConfigurator extends BaseConfigurator
{
    public function __construct()
    {
        $this->name = 'order';
    }
}
