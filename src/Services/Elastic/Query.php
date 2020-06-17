<?php namespace Core\Services\Elastic;

use Core\Http\RequestOptions;
use Illuminate\Support\Arr;

class Query
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
    const DEFAULT_PAGER_SIZE = 1000;

    /*
        Default search string, if none present
    */
    const DEFAULT_LANGUAGE = 'en';

    /*
        Default max number of facets show
    */
    const DEFAULT_MAX_FACETS = 15;

    /*
        Default min document count before a facet is shown
    */
    const DEFAULT_MIN_DOC_COUNT = 1;

    public function handle(
        RequestOptions $options,
        array $facets = [],
        array $range_filters = [],        
        array $range_facets = []
    ) : array {
        $body = [];        
        $this->options = $options;
        $body['index'] = $this->options->getIndex() ? $this->options->getIndex() : config('services.elastic.index');
        $body['size'] = $this->options->getPerPage() ? $this->options->getPerPage() : self::DEFAULT_PAGER_SIZE;
        $body['from'] = $this->options->getPage() ? $body['size'] * ($this->options->getPage() - 1) : self::DEFAULT_PAGER_FROM;
        $body['body']['sort'][][$this->options->getSortColumn()] = $this->options->getSortDirection();

        $filters = null;
        if ($this->options->getFilters()) {
            $filters = $this->buildFilters($this->options->getFilters(), $range_filters);
        }
        if ($this->options->getIds()) {
            $filters[] = (object) $this->buildIdQuery($this->options->getIds());
        }

        /* the search query itself */
        $language = $this->options->getLanguage() ? $this->options->getLanguage() : self::DEFAULT_LANGUAGE;
        $query = $this->buildFields($this->options->getSearchString(), $language, $this->options->getSearchFields());

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
        // print_r(json_encode($body, JSON_PRETTY_PRINT));
        // exit();
        return $body;
    }

    public function buildFields(string $search_string = '', string $language = '', $search_fields = []) : array
    {
        $query = [];
        $search_string = trim($search_string);
        if (!$search_fields) {
            $search_fields = ['search_text'];
        }
        /* search in different ways to ensure we get what we want, sorted as we want */
        if (in_array('name', $search_fields)) {
            $query['bool']['should'][]['term']['name'] = (object) [ 'value' => $search_string, 'boost' => 4000 ];
        }
        if (in_array('search_text', $search_fields)) {                
            $query['bool']['should'][]['term']['search_text'] = (object) [ 'value' => $search_string, 'boost' => 1500 ];
        }
        //  "simple_query_string" (as opposed to "query_string") avoids throwing exceptions on bad input
        $analyzer = ('fr' == $language) ? 'french_standard' : 'english_standard';
        $text_search['query'] = $search_string;        
        if (in_array('name', $search_fields)) {
            $text_search['fields'][] = 'name.'.$analyzer.'^3000';
        }    
        if (in_array('search_text', $search_fields)) {  
            $text_search['fields'][] = 'search_text.'.$analyzer.'^1000';
        }
        $query['bool']['should'][]['simple_query_string'] = (object) $text_search;

        if ('*' == $search_string) {
            $wildcard_string = $search_string;
        } else {
            $wildcard_string = '*' . $search_string . '*';
        }
        if (in_array('name', $search_fields)) {
            $query['bool']['should'][]['wildcard']['name'] = [ 'value' => $wildcard_string, 'boost' => 800 ];
        }
        if (in_array('search_text', $search_fields)) {    
            $query['bool']['should'][]['wildcard']['search_text'] = (object) [ 'value' => $wildcard_string, 'boost' => 600 ];
        }    
        return $query;
    }

    public function buildIdQuery(array $ids) : array
    {
        return [
            'ids' => (object) [ 'values' => $ids ]
        ];
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
        //pr($filters);
        //  format all other terms
        $filters = collect($filters)->each(function ($item, $key) use (&$formatted, $used_filters) {
            if (!in_array($key, $used_filters)) {
                if (is_array($item)) {
                    $formatted[]['terms'][$key] = $item;
                } else {
                    $formatted[]['terms'][$key][] = $item;
                }    
            }
        });
        return $formatted;
    }

    public function makeRange($filters, $from_field, $to_field, $search_field)
    {
        //  ranges are formatted differently than other terms
        $from = Arr::get($filters, $from_field);
        $to = Arr::get($filters, $to_field);
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
        return $range ? (object) [$search_field => $range] : false;
    }

    public function buildFacets(array $body, array $facets = [], array $ranges = []) : array
    {
        //  Build range facets
        foreach ($ranges as $name => $range) {
            $body['body']['aggs'][$name]['range']['field'] = $range['field'];
            foreach ($range['facets'] as $facet) {
                $agg = null;
                if (array_key_exists('from', $facet)) {
                    $agg['from'] = $facet['from'];
                }
                if (array_key_exists('to', $facet)) {
                    $agg['to'] = $facet['to'];
                }
                if (array_key_exists('name', $facet)) {
                    $agg['name'] = $facet['name'];
                }
                if ($agg) {
                    $body['body']['aggs'][$name]['range']['ranges'][] = (object) $agg;
                }
            }
        }

        //   Build term facets
        foreach ($facets as $facet) {
            $body['body']['aggs'][$facet]['terms']['field'] = $facet;
            $body['body']['aggs'][$facet]['terms']['size'] = $this->options->getMaxFacets() ? $this->options->getMaxFacets() : self::DEFAULT_MAX_FACETS;
            $body['body']['aggs'][$facet]['terms']['min_doc_count'] = $this->options->getMinDocCount() ? $this->options->getMinDocCount() : self::DEFAULT_MIN_DOC_COUNT;
            //$body['body']['aggs'][$facet]['terms']['missing'] = "N/A";
        }
        return $body;
    }
}



// protected $range_filters = [
//     'price' => [
//         'from'          => 'price_from',
//         'to'            => 'price_to',
//         'search_field'  => 'retail_price'
//     ]
// ];
// protected $range_facets = [
//     'price_range' => [
//         'field'     => 'retail_price',
//         'facets'    => [
//             [
//                 'from'      => '0',
//                 'to'        => '100',
//                 'name'      => '0-10'
//             ],
//             [
//                 'from'      => '100',
//                 'to'        => '1000',
//                 'name'      => '10-20'
//             ],
//             [
//                 'from'      => '1000',
//                 'to'        => '10000',
//                 'name'      => '20-30'
//             ],
//             [
//                 'from'      => '10000',
//                 'to'        => '100000',
//                 'name'      => '30-40'
//             ],
//             [
//                 'from'      => '1000000',
//                 'to'        => '10000000',
//                 'name'      => '40-50'
//             ]
//         ]
//     ]
// ];