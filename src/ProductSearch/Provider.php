<?php namespace ProductSearch;

use App\Modules\ProductSearch\Query;
use Core\Services\Elastic\Search as SearchEngine;
use App\Support\Cache\Manager as CacheManager;
use Core\ProductSearch\FacetTransformer;
use Api\Models\Product as ProductModel;

class Provider
{
    public function __construct(
        Query $query,
        SearchEngine $engine
    )
    {
        $this->query = $query;
        $this->engine = $engine;
    }

    public function search($search_string, $options = [], $facets = '', $is_cache = false)
    {
        $query = $this->query->buildQuery($search_string, $options, $facets);
        //print_r(json_encode($query, JSON_PRETTY_PRINT));
        
        return !$is_cache ? $this->rawSearch($query) : $this->cachedSearch($query);
    }
    
    protected function rawSearch($query)
    {
        $search = $this->engine->search($query);
        return $this->transform($search->getItems(), $search->getFacets());
    }

    protected function cachedSearch($query)
    {
        $cache              = new CacheManager('memcached', 'search', config('cache.search_timeout_mins'));
        $serialized_query   = md5(serialize($query));
        $raw_results        = $cache->get($serialized_query);
        if ($raw_results && array_key_exists('items', $raw_results) && is_array($raw_results['items'])) {
            $results = $this->transform($raw_results['items'], $raw_results['facets']);
        } else {
            $results = $this->rawSearch($query);
            $timeout = $cache->putTimeout($serialized_query);
            $cache->put($serialized_query . ':'.$timeout, $results);
        }
        return $results;
    }

    protected function transform($items, $facets)
    {
        return [
            'items' => $this->transformProduct($items),
            'facets' => (new FacetTransformer)->transform($facets)
        ];
    }

    protected function transformProduct(array $hits = [])
    {
        return collect($hits)->map(function ($item) {
            if (!empty($item['_source']['content'])) {
                return new ProductModel(json_decode($item['_source']['content'], true));
            }
        })->toArray();
    }    
}