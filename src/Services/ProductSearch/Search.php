<?php namespace App\Modules\ProductSearch;

use App\Modules\ProductSearch\Query;
use App\Support\Elastic\Search as SearchEngine;
use App\Support\Cache\Manager as CacheManager;
use App\Models\Product;
use App\Models\Category;
use App\Models\Site;

class Search
{
    public function __construct(Query $query, SearchEngine $engine)
    {
        $this->query = $query;
        $this->engine = $engine;
    }

    public function search($search_string, $options = [], $facets = '', $is_cache = null)
    {
        $results = [];
        $query = $this->query->buildQuery($search_string, $options, $facets);
        //print_r(json_encode($query, JSON_PRETTY_PRINT));

        if (false == $is_cache) {
            $search = $this->engine->search($query);
            $results['items'] = $this->transformItems($search->getItems());
            $results['facets'] = $this->transformFacets($search->getFacets());
        } else {
            $cache              = new CacheManager('memcached', 'search', config('cache.search_timeout_mins'));
            $query_serialized   = md5(serialize($query));
            $raw_results        = $cache->get($query_string);
            if ($raw_results && isset($raw_results['items'])) {
                if (is_array($raw_results['items'])) {
                    $results['items'] = $this->transformHits($raw_results['hits']);
                    $results['facets'] = $this->transformFacets($raw_results['facets']);
                }
            } else {
                $search = $this->engine->search($query);
                $results['items'] = $this->transformItems($search->getItems());
                $results['facets'] = $this->transformFacets($search->getFacets());
                $timeout = $cache->putTimeout($query_serialized);
                $cache->put($query_string . ':'.$timeout, [
                    'items' => $search->getItems(),
                    'facets' => $search->getFacets()
                ]);
            }
        }
        return $results;
    }
    
    protected function transformItems($hits = [])
    {
        return collect($hits)->map(function ($item) {
            if (!empty($item['_source']['content'])) {
                return new Product(json_decode($item['_source']['content'], true));
            }
        })->toArray();
    }

    //  Slim facet response by removing info not needed in view
    public function transformFacets($facets)
    {
        $facets = collect($facets);
        return $facets->transform(function ($item, $key) {
            if (false !== strpos($key, '_range')) {
                return collect($item['buckets'])->map(function ($bucket) {
                    return (collect($bucket))->only('key', 'doc_count');
                });
            }
            if ('category_id' == $key) {
                return $this->categoryFacets($item['buckets']); 
            } elseif ('site_id' == $key) {
                return $this->siteFacets($item['buckets']);
            } elseif ('retail_price' == $key) {
                //return $this->siteFacets($item['buckets']);
            } elseif ('country_code' == $key) {
                return $this->countryFacets($item['buckets']);
            }
            return $item['buckets'];
        });
    }
    
    public function categoryFacets($buckets)
    {
        return collect($buckets)->map(function ($item) {
            $category = new Category(api('category')->get($item['key']));
            return [
                'key'       => $item['key'],
                'label'     => $category->name(),
                'url'       => $category->url(),
                'doc_count' => $item['doc_count']
            ];
        })->toArray();
    }

    public function siteFacets($buckets)
    {
        return collect($buckets)->map(function ($item) {
            $site = api('site')->get($item['key']);
            if ($site) {
                $site = new Site($site);
                return [
                    'key'       => $item['key'],
                    'label'     => $site->forceName(),
                    'handle'    => $site->handle(),
                    'url'       => $site->url(),
                    'doc_count' => $item['doc_count']
                ];
            }    
        })->toArray();
    }

    public function countryFacets($buckets)
    {
        return collect($buckets)->map(function ($item) {
            return [
                'key'       => $item['key'],
                'label'     => countryName($item['key']),
                'doc_count' => $item['doc_count']
            ];
        })->toArray();
    }

    public function priceFacets($buckets)
    {
        return collect($buckets)->map(function ($item) {
            return [
                'key'       => $item['key'],
                'label'     => '$' . $item['key'],
                'doc_count' => $item['doc_count']
            ];
        })->toArray();
    }    
}