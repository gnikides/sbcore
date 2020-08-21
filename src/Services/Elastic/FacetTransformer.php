<?php namespace Core\Services\Elastic;

class FacetTransformer
{
    public function sluggable($buckets, $collection, $sort = true)
    {   
        $facets = collect($buckets)->map(function ($item) use ($collection) {
            if (array_key_exists('key', $item)) {
                $model = $collection->first(function ($value, $key) use ($item) {
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

    public function productGroup($buckets, $collection, $sort = true)
    {   
        $facets = collect($buckets)->map(function ($item) use ($collection) {
            if (array_key_exists('key', $item)) {
                $model = $collection->first(function ($value, $key) use ($item) {
                    return $value->id == $item['key'];                    
                });
                if ($model && $model->name) {
                    return [
                        'key' => $item['key'],
                        'name' => $model->name,
                        'slug' => isset($model->slug) ? $model->slug : '',
                        'uri' => $model->slug . '/g',
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

    public function category($buckets, $collection, $sort = true)
    {   
        $facets = collect($buckets)->map(function ($item) use ($collection) {
            if (array_key_exists('key', $item)) {
                $model = $collection->first(function ($value, $key) use ($item) {
                    return $value->id == $item['key'];                    
                });
                if ($model && $model->name) {
                    return [
                        'key' => $item['key'],
                        'name' => $model->name,
                        'slug' => isset($model->slug) ? $model->slug : '',
                        'uri' => $model->slug . '/c',
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

    public function priceRange($buckets, $currency, $sort = true)
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
            return sortByValue($facets, 'sort'); 
        }
        return $facets;
    }

    public function name($buckets, $sort = true)
    {  
        $facets = collect($buckets)->map(function ($item) {
            if (array_key_exists('key', $item)) {
                return [
                    'key' => $item['key'],
                    'name' => ucfirst(__($item['key'])),
                    'doc_count' => $item['doc_count']
                ];
            }
        })->toArray();
        if ($sort) {
            return sortByValue($facets, 'name'); 
        }
        return $facets;        
    }

    public function country($buckets, $collection, $sort = true)
    {  
        $facets = collect($buckets)->map(function ($item) use ($collection) {
            $model = $collection->first(function ($value, $key) use ($item) {
                return $value->id == $item['key'];                    
            });
            if (array_key_exists('key', $item)) {
                return [
                    'key' => $item['key'],
                    'name' => isset($model->name) ? $model->name : '',
                    'doc_count' => $item['doc_count']
                ];
            }
        })->toArray();
        if ($sort) {
            return sortByValue($facets, 'name'); 
        }
        return $facets;        
    }

    public function default($buckets, $sort = true)
    {  
        $facets = collect($buckets)->map(function ($item) {
            if (array_key_exists('key', $item)) {
                return [
                    'key' => $item['key'],
                    'name' => $item['key'],
                    'doc_count' => $item['doc_count']
                ];
            }
        })->toArray();
        if ($sort) {
            return sortByValue($facets, 'name'); 
        }
        return $facets;         
    }

    public function stores($buckets, $collection, $sort = true)
    {
        $facets = collect($buckets)->map(function ($item) use ($collection) {
            if (array_key_exists('key', $item)) {            
                $model = $collection->first(function($value, $key) use ($item) {                    
                    return $value->id == $item['key'];                    
                });
                if ($model && $model->forced_name) {
                    return [
                        'key'       => $item['key'],
                        'label'     => $model->forced_name,
                        'handle'    => $model->handle,
                        'url'       => $model->slug,   // @todo fukk urk
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