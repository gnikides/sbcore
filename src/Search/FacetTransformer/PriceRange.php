<?php namespace Core\Search\FacetTransformer;

use Core\Search\FacetTransformer\Base;

class PriceRange extends Base
{
    public function transform($buckets, $sort = true)
    {
        $facets = collect($buckets)->map(function ($item) use ($currency) {
            if (array_key_exists('key', $item)) {
                return [
                    'key' => $item['key'],
                    'sort' => explode('-', $item['key'])[0],
                    'name' => $currency->getSymbol().$item['key'],
                    'doc_count' => $item['doc_count']
                ];
            }
        })->toArray();
        if ($sort) {
            return sortByValue($facets, 'name'); 
        }
        return $facets;
    }
}
