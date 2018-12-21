<?php namespace Core\Services\Elastic;

use Core\Services\Elastic\QueryOptions;

abstract class Query
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
    const DEFAULT_LANGUAGE = 'en';

    /*
        Default sort field
    */
    protected $sort_column = 'updated_at';

    /*
        Default sort direction
    */
    protected $sort_direction = 'desc';

    public function build(
        string $search_string = '',
        array $options = [],
        array $range_filters = [],
        array $facets = [],
        array $range_facets = []
    ) : array {
        $body = [];

        $body['size'] = $options['per_page'] ?? self::DEFAULT_PAGER_SIZE;
        $body['from'] = isset($options['page']) ? $body['size'] * ($options['page'] - 1) : self::DEFAULT_PAGER_FROM;
        $body['body']['sort'][][$this->sortColumn($options)] = $this->sortDirection($options);

        $filters = null;
        if (array_key_exists('filters', $options) && method_exists($this, 'buildFilters')) {
            $filters = $this->buildFilters($options['filters'], $range_filters);
        }
        if (array_key_exists('ids', $options) && !empty($options['ids'])) {
            $filters[] = (object) $this->buildIdQuery($options['ids']);
        }

        // /* the search query itself */
        $language = $options['lang'] ?? self::DEFAULT_LANGUAGE;
        $query = $this->buildSearchFields($search_string, $language);

        /* if there are filters, query will be structured differently */
        if (is_array($filters)) {
            foreach ($filters as $filter) {
                $body['body']['query']['bool']['filter'][] = (object) $filter;
            }
            $body['body']['query']['bool']['filter'][] = (object) $query;
        } else {
            $body['body']['query'] = (object) $query;
        }

        // /* add facets or aggregations, if any */
        if ($facets) {
            $body = $this->buildFacets($body, $facets, $range_facets);
        }

        //  @debug as json, then view in kibana, for instance
        //  print_r(json_encode($body, JSON_PRETTY_PRINT));
        //  exit();
        return $body;
    }

    public function buildIdQuery(array $ids) : array
    {
        return [
            'ids' => (object) [ 'values' => $ids ]
        ];
    }

    public function buildSearchFields(string $search_string = '', string $language = '') : array
    {
        $query = [];
        return $query;
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
        // foreach ($ranges as $name => $range) {
        //     $body['body']['aggs'][$name]['range']['field'] = $range['field'];
        //     foreach ($range['facets'] as $facet) {
        //         $agg = null;
        //         if (array_key_exists('from', $facet)) {
        //             $agg['from'] = $facet['from'];
        //         }
        //         if (array_key_exists('to', $facet)) {
        //             $agg['to'] = $facet['to'];
        //         }
        //         if (array_key_exists('name', $facet)) {
        //             $agg['name'] = $facet['name'];
        //         }
        //         if ($agg) {
        //             $body['body']['aggs'][$name]['range']['ranges'][] = (object) $agg;
        //         }
        //     }
        // }
        //   Build term facets
        foreach ($facets as $facet) {
            $body['body']['aggs'][$facet]['terms']['field'] = $facet;
        }
        return $body;
    }

    public function sortColumn(array $params) : string
    {
        return array_get($params, 'sort_column', $this->sort_column);
    }

    public function sortDirection(array $params) : string
    {
        return array_get($params, 'sort_direction', $this->sort_direction);
    }
}
