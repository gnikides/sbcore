<?php namespace Core\Search\Order;

use Core\Services\Elastic\Query as BaseQuery;
use Core\Http\RequestOptions;

class Query extends BaseQuery
{
    protected $range_filters = [
        'updated_at' => [
            'from'          => 'updated_at_from',
            'to'            => 'updated_to_to',
            'search_field'  => 'updated_at'
        ]
    ];

    public function buildQuery(RequestOptions $options) : array
    {
        return $this->build($options, $this->range_filters);
    }

    public function buildSearchFields(string $search_string = '', string $language = '') : array
    {
        $query = [];
        $search_string = trim($search_string);

        /* search in different ways to ensure we get what we want, sorted as we want */
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
        $query['bool']['should'][]['wildcard']['search_text'] = (object) [ 'value' => $wildcard_string, 'boost' => 600 ];

        return $query;
    }
}
