<?php namespace Core\Search\FacetTransformer;

use Core\Search\FacetTransformer\Base;

class Toponomy extends Base
{
    public function transform($buckets, $sort = true)
    {
        $facets = collect($buckets)->map(function ($item) {
            if (array_key_exists('key', $item)) {
                $model = $this->collection->first(function ($value, $key) use ($item) {
                    return $value->id == $item['key'];                    
                });
                if ($model && $model->name) {
                    return [
                        'key' => $item['key'],
                        'name' => $model->name,
                        'slug' => isset($model->slug) ? $model->slug : '',
                        'doc_count' => $item['doc_count']
                    ];
                }
            }
        })->toArray();
        if ($sort) {
            return sortByValue($facets, 'name'); 
        }
        return $facets;
    }
}
