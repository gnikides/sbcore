<?php namespace Core\ProductSearch;

class FacetTransformer
{
    public function transform($facets, $stores, $categories, $countries)
    {   
        return collect($facets)->transform(function ($item, $key) use ($stores, $categories, $countries) {
            if (false !== strpos($key, '_range')) {
                return collect($item['buckets'])->map(function ($bucket) {
                    return (collect($bucket))->only('key', 'doc_count');
                });
            }
            if ('category_id' == $key) {
                return $this->categories($item['buckets'], $categories);
            } elseif ('store_id' == $key) {
                return $this->stores($item['buckets'], $stores);
            } elseif ('retail_price' == $key) {
                //return $this->siteFacets($item['buckets'], $currency);
            } elseif ('country_code' == $key) {
                return $this->countries($item['buckets'], $countries);
            }
            return $item['buckets'];
        });
    }

    public function categories($buckets, $categories)
    {  
        return collect($buckets)->map(function ($item) use ($categories) {
            if (array_key_exists('key', $item)) {
                $category = array_key_exists($item['key'], $categories) ? $categories[$item['key']] : null;
                if ($category && $category->name() && $category->url()) {
                    return [
                        'key'       => $item['key'],
                        'label'     => $category->name(),
                        'url'       => $category->url(),
                        'doc_count' => $item['doc_count']
                    ];
                }
            }
        })->toArray();
    }

    public function stores($buckets, $stores)
    {
        return collect($buckets)->map(function ($item) use ($stores) {
            if (array_key_exists('key', $item)) {
                $store = array_key_exists($item['key'], $stores) ? $stores[$item['key']] : null;
                if ($store) {
                    return [
                        'key'       => $item['key'],
                        //'label'     => $site->forceName(),
                        //'handle'    => $site->handle(),
                        //'url'       => $site->url(),
                        'doc_count' => $item['doc_count']
                    ];
                }
            }
        })->toArray();
    }

    public function countries($buckets, $countries)
    {
        return collect($buckets)->map(function ($item) use ($countries) {
            if (array_key_exists('key', $item)) {
                $country = array_key_exists($item['key'], $countries) ? $countries[$item['key']] : null;
                if ($country) {
                    return [
                        'key'       => $item['key'],
                        'label'     => $country->name,
                        'doc_count' => $item['doc_count']
                    ];
                }
            }
        })->toArray();
    }

    public function prices($buckets, $currency)
    {
        //   how put currency in there ????
        return collect($buckets)->map(function ($item) {
            return [
                'key'       => $item['key'],
                'label'     => '$' . $item['key'],
                'doc_count' => $item['doc_count']
            ];
        })->toArray();
    } 
}
