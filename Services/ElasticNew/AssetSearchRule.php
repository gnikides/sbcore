<?php

namespace App\Services\ElasticService;

use ScoutElastic\SearchRule;

class AssetSearchRule extends SearchRule
{
    public function buildQueryPayload()
    {
        return [
            'should' => [
                [
                    'match' => [
                        'title' => [
                            'query' => $this->builder->query,
                            'boost' => 2
                        ]
                    ]
                ],
                [
                    'match' => [
                        'search_text' => [
                            'query' => $this->builder->query,
                            'boost' => 1
                        ]
                    ]
                ]
            ]
         ];
    }
}
