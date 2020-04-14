<?php namespace Core\Search\Product;

class FacetTransformer
{
    public function transform($facets, $data = [], $currency = null)
    {   
        return collect($facets)->transform(function ($item, $key) use ($data, $currency) {
            // if (false !== strpos($key, '_range')) {
            //     return collect($item['buckets'])->map(function ($bucket) {
            //         return (collect($bucket))->only('key', 'doc_count');
            //     });
            // }
            if ('product_group_id' == $key && array_key_exists('groups', $data)) {
                return $this->productGroups($item['buckets'], $data['groups']);
            } elseif ('category_id' == $key && array_key_exists('categories', $data)) {
                return $this->categories($item['buckets'], $data['categories']);
            } elseif ('price_range' == $key) {
                return $this->priceRanges($item['buckets'], $currency);                
            // } elseif ('store_id' == $key) {
            //     return $this->stores($item['buckets'], $stores);
            // } elseif ('retail_price' == $key) {
            //     //return $this->siteFacets($item['buckets'], $currency);
            // } elseif ('country_code' == $key) {
            //     return $this->countries($item['buckets'], $countries);
            }  
        })->reject(function ($item) {
            return is_null($item);
        });
    }

    public function productGroups($buckets, $groups)
    {
        return collect($buckets)->map(function ($item) use ($groups) {
            if (array_key_exists('key', $item)) {
                $group = $groups->first(function ($value, $key) use ($item) {
                    return $value->id == $item['key'];                    
                });
                if ($group && $group->name) {
                    return [
                        'key' => $item['key'],
                        'name' => $group->name,
                        'slug' => isset($group->slug) ? $group->slug : '',
                        'doc_count' => $item['doc_count']
                    ];
                }
            }
        })->toArray();
    }

    public function categories($buckets, $categories)
    {  
        return collect($buckets)->map(function ($item) use ($categories) {
            if (array_key_exists('key', $item)) {
                $category = $categories->first(function ($value, $key) use ($item) {
                    return $value->id == $item['key'];                    
                });
                if ($category && $category->name) {
                    return [
                        'key' => $item['key'],
                        'name' => $category->name,
                        'slug' => isset($category->slug) ? $category->slug : '',
                        'doc_count' => $item['doc_count']
                    ];
                }
            }
        })->toArray();
    }

    public function priceRanges($buckets, $currency)
    {  
        return collect($buckets)->map(function ($item) use ($currency) {
            if (array_key_exists('key', $item)) {
                return [
                    'key' => $item['key'],
                    'name' => $currency->getSymbol().$item['key'],
                    'doc_count' => $item['doc_count']
                ];
            }
        })->toArray();
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

    // public function prices($buckets, $currency)
    // {
    //     //   how put currency in there ????
    //     return collect($buckets)->map(function ($item) {
    //         return [
    //             'key'       => $item['key'],
    //             'label'     => '$' . $item['key'],
    //             'doc_count' => $item['doc_count']
    //         ];
    //     })->toArray();
    // } 
}
