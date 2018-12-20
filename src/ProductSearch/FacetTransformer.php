<?php namespace Core\ProductSearch;

class FacetTransformer
{
    public function transform($facets, array $sites, array $categories, array $countries) : array
    {
        return collect($facets)->transform(function ($item, $key) {
            if (false !== strpos($key, '_range')) {
                return collect($item['buckets'])->map(function ($bucket) {
                    return (collect($bucket))->only('key', 'doc_count');
                });
            }
            if ('category_id' == $key) {
                return $this->categories($item['buckets'], $categories);
            } elseif ('site_id' == $key) {
                return $this->sites($item['buckets'], $sites);
            } elseif ('retail_price' == $key) {
                //return $this->siteFacets($item['buckets'], $currency);
            } elseif ('country_code' == $key) {
                return $this->countries($item['buckets'], $countries);
            }
            return $item['buckets'];
        });
    }

    public function categories($buckets, array $categories)
    {
        return collect($buckets)->map(function ($item) use ($categories) {
            if (array_key_exists('key', $item)) {
                $category = array_key_exists($item['key'], $categories) ? $categories[$item['key']] : null;
                if ($category->name() && $category->url()) {
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

    public function sites($bucket, array $sites)
    {
        return collect($buckets)->map(function ($item) use ($sites) {
            if (array_key_exists('key', $item)) {
                $site = array_key_exists($item['key'], $sites) ? $sites[$item['key']] : null;
                if ($site->handle() && $site->url()) {
                    return [
                        'key'       => $item['key'],
                        'label'     => $site->forceName(),
                        'handle'    => $site->handle(),
                        'url'       => $site->url(),
                        'doc_count' => $item['doc_count']
                    ];
                }
            }
        })->toArray();
    }

    public function countries($buckets, array $countries)
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
        //   how rto put currency in there ????
        return collect($buckets)->map(function ($item) {
            return [
                'key'       => $item['key'],
                'label'     => '$' . $item['key'],
                'doc_count' => $item['doc_count']
            ];
        })->toArray();
    } 

    // protected function bankFacet($bucket)
    // {
    //     $bank = $this->setupService->banks->find($bucket['key']);
    //     return [
    //         'key' => $bucket['key'],
    //         'label' => ('none' == $bucket['key']) ? __('None') : $bank['name'],
    //         'doc_count' => $bucket['doc_count'],
    //     ];
    // }

    // protected function dateRangeFacet($bucket)
    // {
    //     return [
    //         'key' => $bucket['key'],
    //         'from' => $bucket['from_as_string'],
    //         'doc_count' => $bucket['doc_count'],
    //     ];
    // }

    // protected function statusFacet($bucket)
    // {
    //     $status = $this->statuses->filter(function ($item) use ($bucket) {
    //         return $item->id == $bucket['key'];
    //     })->first();
    //     return [
    //         'key' => $bucket['key'],
    //         'label' => __($status->name),
    //         'doc_count' => $bucket['doc_count']
    //     ];
    // }

    // protected function assetTypeFacet($bucket)
    // {
    //     $type = $this->setupService->assetTypes->findBySlug($bucket['key']);
    //     return [
    //         'key' => $bucket['key'],
    //         'label' => __($type->name),
    //         'doc_count' => $bucket['doc_count']
    //     ];
    // }

    // protected function commonTextKeyFacet($bucket)
    // {
    //     return [
    //         'key' => $bucket['key'],
    //         'label' => ucfirst(__($bucket['key'])),
    //         'doc_count' => $bucket['doc_count']
    //     ];
    // }

    // public function setLabels($setupService, $statuses, $classifications)
    // {
    //     $this->setupService = $setupService;
    //     $this->statuses = $statuses;
    //     $this->classifications = $classifications;
    //     return $this;
    // }
}
