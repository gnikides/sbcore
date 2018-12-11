<?php

namespace App\Services;

class ElasticService
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
        Default sort field
    */
    const SORT_COLUMN = 'uploaded_at';

    /*
        Default sort direction
    */
    const SORT_DIRECTION = 'asc';

    /*
        Default date filter
    */
    const DATE_FILTER = 'uploaded_at';

    /*
        Default search string, if none present
    */
    const DEFAULT_LANGUAGE = 'fr';

    public function buildSearch(array $params = [], array $facets = [], array $ranges = []) : array
    {
        $body = [];

        /* query options */
        $body['size'] = isset($params['per_page']) ? $params['per_page'] : self::DEFAULT_PAGER_SIZE;
        $body['from'] = isset($params['page']) ? $body['size'] * ($params['page'] - 1) : self::DEFAULT_PAGER_FROM;
        $body['sort'][][$this->sortColumn($params)] = $this->sortDirection($params);

        /* query filters */
        $filters = null;
        if (array_key_exists('filters', $params)) {
            $filters = $this->buildFilters($params['filters'], self::DATE_FILTER);
        }

        /* the search query itself */
        $query = $this->buildQuery($params);

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
            $body = $this->buildFacets($body, $facets, $ranges);
        }

        /* @debug as json, then view in kibana, for instance */
        // print_r(json_encode($body, JSON_PRETTY_PRINT));
        // exit();
        
        return $body;
    }

    public function buildQuery(array $params) : array
    {
        $query = [];
        $search = $this->searchString($params);

        /* search in different ways to ensure we get what we want, sorted as we want */
        $query['bool']['should'][]['term']['title'] = (object) [ 'value' => $search, 'boost' => 4000 ];
        $query['bool']['should'][]['term']['search_text'] = (object) [ 'value' => $search, 'boost' => 1500 ];

        //  "simple_query_string" (as opposed to "query_string") avoids throwing exceptions on bad input
        $language = $this->language($params);
        $analyzer = ('en' == $language) ? 'english_standard' : 'french_standard';
        $text_search['query'] = $search;
        $text_search['fields'] = ['title.' . $analyzer . '^3000', 'search_text.' . $analyzer . '^1000'];
        $query['bool']['should'][]['simple_query_string'] = (object) $text_search;

        $query['bool']['should'][]['wildcard']['title'] = [ 'value' => '*' . $search . '*', 'boost' => 800 ];
        $query['bool']['should'][]['wildcard']['search_text'] = (object) [ 'value' => '*' . $search . '*', 'boost' => 600 ];

        return $query;
    }

    public function buildFilters($filters, string $date_filter = 'uploaded_at') : array
    {
        $formatted = null;

        //  ranges are formatted differently than other terms
        $date_from = array_get($filters, 'date_from');
        $date_to = array_get($filters, 'date_to');
        $date_range = null;
        if ($date_from && $date_to) {
            $date_range = (object) [
                'from'  => $date_from,
                'to'    => $date_to
            ];
        } elseif ($date_from) {
            $date_range['from'] = $date_from;
        } elseif ($date_to) {
            $date_range['to'] = $date_to;
        }
        if ($date_range) {
            $formatted[]['range'] = (object) [$date_filter => $date_range];
        }

        //  format all other terms
        $filters->each(function ($item, $key) use (&$formatted) {
            if (('date_from' != $key) && ('date_to' != $key)) {
                $formatted[]['terms'][$key][] = $item;
            }
        });
        return $formatted;
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

    public function search($model, array $relations = [], array $params = [], array $facets = [], array $ranges = []) : array
    {
        $response = $model::searchRaw(
            $this->buildSearch($params, $facets, $ranges)
        );
        $ids = collect($this->getItems($response))->pluck('id')->toArray();
        $hits = $model::withTrashed()->whereIn('_id', $ids)->with($relations)->get();
        $hits = $hits->sortBy(function ($hit) use ($ids) {
            return array_search($hit->id, $ids);
        });
        return [
            'hits' => $hits,
            'count' => $this->getCount($response),
            'facets' => $this->transformFacets($response)
        ];
    }

    public function language(array $params) : string
    {
        return !empty($params['lang']) ? $params['lang'] : self::DEFAULT_LANGUAGE;
    }

    public function searchString(array $params) : string
    {
        return !empty($params['q']) ? $params['q'] : self::DEFAULT_SEARCH;
    }

    public function sortColumn(array $params) : string
    {
        return array_get($params, 'sort_column', self::SORT_COLUMN);
    }

    public function sortDirection(array $params) : string
    {
        return array_get($params, 'sort_direction', self::SORT_DIRECTION);
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
    public function transformFacets(array $response)
    {
        $facets = collect($this->getFacets($response));
        return $facets->transform(function ($item, $key) {
            if (false !== strpos($key, '_range')) {
                return collect($item['buckets'])->map(function ($bucket) {
                    return (collect($bucket))->only('key', 'doc_count');
                });
            }
            return $item['buckets'];
        });
    }
}
