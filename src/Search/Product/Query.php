<?php namespace Core\Search\Product;

use Core\Services\Elastic\Query as BaseQuery;
use Core\Http\RequestOptions;

class Query extends BaseQuery
{
    protected $range_filters = [
        'price' => [
            'from'          => 'price_from',
            'to'            => 'price_to',
            'search_field'  => 'retail_price'
        ]
    ];
    protected $range_facets = [
        'price_range' => [
            'field'     => 'retail_price',
            'facets'    => [
                [
                    'from'      => '0',
                    'to'        => '100',
                    'name'      => '0-10'
                ],
                [
                    'from'      => '100',
                    'to'        => '1000',
                    'name'      => '10-20'
                ],
                [
                    'from'      => '1000',
                    'to'        => '10000',
                    'name'      => '20-30'
                ],
                [
                    'from'      => '10000',
                    'to'        => '100000',
                    'name'      => '30-40'
                ],
                [
                    'from'      => '1000000',
                    'to'        => '10000000',
                    'name'      => '40-50'
                ]
            ]
        ]
    ];

    public function buildQuery(
        RequestOptions $options,
        array $facets = [],
        array $range_facets = []
    ) : array {
        $range_facets = (null === $range_facets) ? $this->range_facets : $range_facets;
        return $this->build($options, $this->range_filters, $facets, $range_facets);
    }

    public function buildSearchFields(string $search_string = '', string $language = '') : array
    {
        $query = [];
        $search_string = trim($search_string);

        /* search in different ways to ensure we get what we want, sorted as we want */
        $query['bool']['should'][]['term']['name'] = (object) [ 'value' => $search_string, 'boost' => 4000 ];
        $query['bool']['should'][]['term']['search_text'] = (object) [ 'value' => $search_string, 'boost' => 1500 ];

        //  "simple_query_string" (as opposed to "query_string") avoids throwing exceptions on bad input
        $analyzer = ('fr' == $language) ? 'french_standard' : 'english_standard';
        $text_search['query'] = $search_string;
        $text_search['fields'] = ['name.' . $analyzer . '^3000', 'search_text.' . $analyzer . '^1000'];
        $query['bool']['should'][]['simple_query_string'] = (object) $text_search;

        if ('*' == $search_string) {
            $wildcard_string = $search_string;
        } else {
            $wildcard_string = '*' . $search_string . '*';
        }
        $query['bool']['should'][]['wildcard']['name'] = [ 'value' => $wildcard_string, 'boost' => 800 ];
        $query['bool']['should'][]['wildcard']['search_text'] = (object) [ 'value' => $wildcard_string, 'boost' => 600 ];

        return $query;
    }
}
