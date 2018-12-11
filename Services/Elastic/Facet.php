<?php namespace App\Modules\Elastic;

use App\Models\Site;

class Facet
{ 
    /**
     * Class options
     *
     * @var array
     */ 
    private $options = [];

    /**
     * Setter for display options
     *
     * @param array $options   
     * @return array
     */ 
    public function setOptions($options=[])
    {
        $this->options = $options;
    }

    /**
     * Format facets for display
     *
     * @param $results
     * @return array
     * @internal param object $search_results
     */
    public function format($results)
    {
        $facets = [];
        
        if (array_key_exists('filtered', $results)) {
            $results = $results['filtered'];
        }
        foreach ($this->options as $option) {
            
            $facet_name = $option['field'] . '_facet';
                        
            $all = [];
            
            if (isset($results[$facet_name])) {

                $buckets = $results[$facet_name]['buckets'];    

                foreach ($buckets as $bucket) {

                    if (!empty($bucket['key']) && !empty($bucket['doc_count'])) {
                        
                        $fields = [];               
                        
                        $fields['key']          = trim($bucket['key']);                 
                        $fields['count']        = trim($bucket['doc_count']);

                        if (@$option['callback']) { 
                            $fields['label']    = trim($this->{$option['callback']}($bucket['key']));
                        } else {
                            $fields['label']    = trim($bucket['key']);
                        }
                        if (!empty($fields['label']) && '$false' != $fields['label']) {
                            $all[] = $fields;
                        }                                               
                    }                   
                }
            }
            
            $sort = [];
            
            if ('price_range' == $option['field']) {                
                $ranges = array_flip(config('store.price_facets'));                         
                foreach ($all as $k => $facet) {
                    $sort[$k] = $ranges[$facet['key']]; 
                }
                
            } else {
                foreach ($all as $k => $facet) {
                    $sort[$k] = $facet['label'];
                }
            }   

            array_multisort($sort, SORT_ASC, $all);
            
            \Log::debug(__FILE__, [json_encode($all, JSON_PRETTY_PRINT)]);              
            
            $facets[$option['field']]['results']    = $all;         
            $facets[$option['field']]['title']      = $option['title'];
        }
        return $facets;
    }

    /**
     * Get category name
     *
     * @param integer $id   
     * @return string
     */    
    protected function categoryName($id)
    {
        return categoryName($id);
    }
 
    /**
     * Get country name
     *
     * @param integer $id    
     * @return string
     */     
    protected function countryName($id)
    {
        return countryName($id);
    } 

    /**
     * Format price range
     *
     * @param string $range  
     * @return string
     */     
    protected function priceRange($range)
    {
        return app('currency')->getSymbol() . $range;
    }

    /**
     * Get site name
     *
     * @param string $handle    
     * @return string
     */     
    protected function siteName($handle)
    {
        $site = new Site(api('site')->get($handle));
        return ucfirst(strtolower($site->forceName()));
    }               
}   