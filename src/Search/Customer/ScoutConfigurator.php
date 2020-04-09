<?php namespace Core\OrderSearch;

use Core\Services\Elastic\BaseConfigurator;

class ScoutConfigurator extends BaseConfigurator
{
    public function __construct()
    {
        $this->name = 'customer';
    }
}
