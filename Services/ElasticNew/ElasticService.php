<?php

namespace App\Services;

abstract class ElasticService
{
    /*
        Default search string, if none present
    */
    const DEFAULT_SEARCH = '*';

    /*
        Default pager position to start search
    */
    const DEFAULT_PAGER_FROM = 0;

    /*
        Default pager result limit
    */
    const DEFAULT_PAGER_SIZE = 500;

    /*
        Default search string, if none present
    */
    const DEFAULT_LANGUAGE = 'fr';

    /*
        Default sort field
    */
    public $sort_column = 'updated_at';

    /*
        Default sort direction
    */
    public $sort_direction = 'desc';

    public function buildSearch(
        string $search_string = '',
        array $options = [],
        array $range_filters = [],
        array $facets = [],
        array $range_facets = []
    ) : array {
        $body = [];
          
        /* query options */
        $body['size'] = $options['per_page'] ?? self::DEFAULT_PAGER_SIZE;
        $body['from'] = isset($options['page']) ? $body['size'] * ($options['page'] - 1) : self::DEFAULT_PAGER_FROM;
        $body['sort'][][$this->sortColumn($options)] = $this->sortDirection($options);

        /* query filters */
        $filters = null;
        if (array_key_exists('filters', $options)) {
            $filters = $this->buildFilters($options['filters'], $range_filters);
        }

        /* the search query itself */
        $language = $options['lang'] ?? self::DEFAULT_LANGUAGE;
        $query = $this->buildQuery($search_string, $language);

        /* if there are filters, query will be structured differently */
        if (is_array($filters)) {
            foreach ($filters as $filter) {
                $body['query']['bool']['filter'][] = (object) $filter;
            }
            $body['query']['bool']['filter'][] = (object) $query;
        } else {
            $body['query'] = (object) $query;
        }

        /* add facets or aggregations, if any */
        if ($facets) {
            $body = $this->buildFacets($body, $facets, $range_facets);
        }

        /* @debug as json, then view in kibana, for instance */
       // print_r(json_encode($body, JSON_PRETTY_PRINT));
       // exit();
        
        return $body;
    }

    public function buildQuery(string $search_string = '', string $language = '') : array
    {
        $query = [];
        return $query;
    }

    public function buildFilters($filters, array $range_params = []) : array
    {
        $formatted = null;
        $used_filters = [];

        //  ranges are formatted differently than other terms
        if ($range_params) {
            foreach ($range_params as $config) {
                if (empty($config['from']) || empty($config['to']) || empty($config['search_field'])) {
                    throw new \Exception('Range filter configuration is incorrect');
                }
                $range = $this->makeRange($filters, $config['from'], $config['to'], $config['search_field']);
                if ($range) {
                    $formatted[]['range'] = $range;
                }
                $used_filters[] = $config['from'];
                $used_filters[] = $config['to'];
            }
        }

        //  format all other terms
        $filters->each(function ($item, $key) use (&$formatted, $used_filters) {
            if (!in_array($key, $used_filters)) {
                $formatted[]['terms'][$key][] = $item;
            }
        });
        return $formatted;
    }

    public function makeRange($filters, $from_field, $to_field, $search_field)
    {
        //  ranges are formatted differently than other terms
        $from = array_get($filters, $from_field);
        $to = array_get($filters, $to_field);
        $range = null;
        if ($from && $to) {
            $range = (object) [
                'from'  => $from,
                'to'    => $to
            ];
        } elseif ($from) {
            $range['from'] = $from;
        } elseif ($to) {
            $range['to'] = $to;
        }
        if ($range) {
            return (object) [$search_field => $range];
        }
        return false;
    }

    public function buildFacets(array $body, array $facets = [], array $ranges = []) : array
    {
        //  Build range facets
        foreach ($ranges as $name => $range) {
            $body['aggs'][$name]['range']['field'] = $range['field'];
            foreach ($range['facets'] as $facet) {
                $body['aggs'][$name]['range']['ranges'][] = (object) [
                    $facet['key']   => $facet['value'],
                    'key'           => $facet['name']
                ];
            }
        }
        //   Build term facets
        foreach ($facets as $facet) {
            $body['aggs'][$facet]['terms']['field'] = $facet;
        }
        return $body;
    }

    public function language(array $params) : string
    {
        return $params['lang'] ?? self::DEFAULT_LANGUAGE;
    }

    public function searchString(array $params) : string
    {
        return $params['q'] ?? self::DEFAULT_SEARCH;
    }

    public function sortColumn(array $params) : string
    {
        return array_get($params, 'sort_column', $this->sort_column);
    }

    public function sortDirection(array $params) : string
    {
        return array_get($params, 'sort_direction', $this->sort_direction);
    }

    public function getCount(array $response) : int
    {
        if (array_key_exists('hits', $response) && array_key_exists('total', $response['hits'])) {
            return !empty($response['hits']['total']) ? $response['hits']['total'] : 0;
        }
    }

    public function getItems(array $response) : array
    {
        if (array_key_exists('hits', $response) && array_key_exists('hits', $response['hits'])) {
            return collect($response['hits']['hits'])->pluck('_source')->all();
        }
        return [];
    }

    public function getFacets(array $response) : array
    {
        return array_key_exists('aggregations', $response) ? $response['aggregations'] : [];
    }

    //  Slim facet response by removing info not needed in view
    // public function transformFacets(array $response)
    // {
    //     $facets = collect($this->getFacets($response));
    //     return $facets->transform(function ($item, $key) {
    //         if (false !== strpos($key, '_range')) {
    //             return collect($item['buckets'])->map(function ($bucket) {
    //                 return (collect($bucket))->only('key', 'doc_count');
    //             });
    //         }
    //         return $item['buckets'];
    //     });
    // }
}
