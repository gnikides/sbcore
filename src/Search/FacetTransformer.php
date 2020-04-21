<?php namespace Core\Search;

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

    // public function stores($buckets, $stores)
    // {
    //     return collect($buckets)->map(function ($item) use ($stores) {
    //         if (array_key_exists('key', $item)) {
    //             // $store = array_key_exists($item['key'], $stores) ? $stores[$item['key']] : null;
    //             // if ($store) {
    //                 return [
    //                     'key'       => $item['key'],
    //                     'label'     => $item['key'],
    //                     //'label'     => $site->forceName(),
    //                     //'handle'    => $site->handle(),
    //                     //'url'       => $site->url(),
    //                     'doc_count' => $item['doc_count']
    //                 ];
    //             //}
    //         }
    //     })->toArray();
    // }

    // public function countries($buckets, $countries)
    // {
    //     return collect($buckets)->map(function ($item) use ($countries) {
    //         if (array_key_exists('key', $item)) {
    //             $country = array_key_exists($item['key'], $countries) ? $countries[$item['key']] : null;
    //             if ($country) {
    //                 return [
    //                     'key'       => $item['key'],
    //                     'label'     => $country->name,
    //                     'doc_count' => $item['doc_count']
    //                 ];
    //             }
    //         }
    //     })->toArray();
    // }
}
