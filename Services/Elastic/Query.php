<?php namespace App\Modules\Elastic;

use App\Support\Foundation\Arr;

class Query
{ 
    /**
     * Search index
     *
     * @var string
     */ 
    private $index; 
    
    /**
     * Search type (like table in db)
     *
     * @var array
     */ 
    private $type;  
        
    /**
     * Query terms
     *
     * @var array
     */ 
    private $terms; 

    /**
     * Main query filters
     *
     * @var array
     */ 
    private $filters;

    /**
     * Filter tags
     *
     * @var array
     */ 
    private $facets;
        
    /**
     * Filter tags
     *
     * @var array
     */ 
    private $tags;

    /**
     * Search options
     *
     * @var array
     */ 
    private $options;
    
    /**
     * Seahc query
     *
     * @var array
     */ 
    private $query;

    /**
     * Create a new instance.
     *
     */
    public function __construct()
    {       
        $this->options['start']                 = 0;
        $this->options['limit']                 = 25;
        $this->options['sort']                  = ['_score:asc'];
        
        // dont hang PHP on error
        $this->options['timeout']               = 2000;
        
        $this->options['fields_to_show']        = [];
        
        // example: ['product_name^3',  'text', 'category_name']
        $this->options['fields_to_search']      = [];
  
        $this->options['highlight']             = [];
        
        // for fuzziness see: https://www.elastic.co/guide/en/elasticsearch/guide/current/fuzziness.html
        $this->options['fuzziness']             = '1';
    }

    /**
     * Make query
     *
     * @return string
     */ 
    public function make()
    {                  
        $this->query['index']                   = $this->index;
        $this->query['type']                    = $this->type;
        $this->query['size']                    = $this->options['limit'];
        $this->query['from']                    = $this->options['start'];      
        $this->query['_source']                 = $this->options['fields_to_show'];
        $this->query['sort']                    = $this->options['sort'];
        
        $this->makeGlobalFilters();
        $this->makeSearchOnlyFilters();
        $this->makeFacets();
        
        \Log::debug(__FILE__, [json_encode($this->query, JSON_PRETTY_PRINT)]);

        return $this->query;
    }  

    /**
     * Make filters that apply to both search and facets
     *
     * @return string
     */
    public function makeGlobalFilters()
    {
        $filters = $this->prepareFilters($this->filters, 'global');

        if (count($filters) > 0) {
            foreach ($filters as $v) {
                $this->query['body']['query']['bool']['must'][] = $v;
            }
        }
                            
        //  no search terms, so show everything ...     
        if (!empty($this->terms)) {      
            
            //$match['fuzziness']   = $this->options['fuzziness'];

            $query = [];
            $query['query']     = $this->terms;
            $query['fields']    = $this->options['fields_to_search'];

            $this->query['body']['query']['bool']['must'][]['simple_query_string'] = $query;

            if (count($this->options['highlight']) > 0) {
                $highlighted = [];
                foreach ($this->options['highlight'] as $field) {
                    $highlighted[$field] = new \stdClass();
                }
                $this->query['body']['highlight'] = [
                    'pre_tags' => ["<em>"],
                    'post_tags' => ["</em>"],
                    'fields' => $highlighted,
                    'require_field_match' => false
                ];
            }   
        }
    }

    /**
     * Make filters that apply only to search (not to facets)
     *
     * @return string
     */ 
    public function makeSearchOnlyFilters()
    {
        $filters = $this->prepareFilters($this->filters, 'search');

        if (count($filters) > 0) {
            foreach ($filters as $v) {
                //$this->query['body']['post_filter']['bool']['should'][] = $v;
                $this->query['body']['post_filter']['bool']['must'][] = $v;
            }
        }               
        return $this->query;        
    }

    /**
     * Make facet query
     *
     * @return string
     */ 
    public function makeFacets()
    {
        if ($this->facets) {
            foreach ($this->facets as $facet) {
                if (isset($facet['field'])) {
                    $this->query['body']['aggs'][$facet['field'] . '_facet']['terms']['field'] = $facet['field'];
                }               
            }           
        }                   
        return $this->query;        
    }
    
    /**
     * Format filters for json query
     *
     * @param array $filters
     * @param string $scope
     * @return mixed
     */ 
    public function prepareFilters($filters=[], $scope='global')
    {   
        $sorted     = [];
        $all        = [];
        if ($filters) {
            foreach ($filters as $filter) {             
                if ('' != $filter['value'] && $scope == $filter['scope']) {                 
                    $sorted[$filter['type']][$filter['key']][] = $filter['value'];
                }   
            }
        }
        foreach ($sorted as $type => $val) {
            foreach ($val as $k => $v) {
                $all[] = [
                    $type => [
                        $k => $v
                    ]                   
                ];
            }   
        }
        return $all;
    }

    /**
     * Setter for filter
     *    
     * @param string $key
     * @param mixed  $value
     * @param string $scope  
     * @param string $type
     * @return object            
    */ 
    public function setFilter($key, $value, $scope='global', $type='terms')
    { 
        $this->filters[] = [
            'key'       => $key,
            'value'     => $value,
            'type'      => $type,
            'scope'     => $scope           
        ];  
        return $this;           
    }

    /**
     * Setter for filters
     *    
     * @param string $key
     * @param array  $values
     * @param string $scope  
     * @param string $type
     * @return object  
    */        
    public function setFilters($key, $values=[], $scope='global', $type='terms')
    {
        foreach ($values as $value) {
            $this->setFilter($key, $value, $scope, $type);
        }
        return $this; 
    }

    /**
     * Getter for filters
     *    
     * @return array
     */     
    public function getFilters()
    { 
        return $this->filters;           
    }
    
    /**
     * Format filters for use in templates
     *    
     * @return array
     */     
    public function formatFilters()
    {   
        $filters = [];
        if (is_array($this->filters)) {
            foreach ($this->filters as $k => $v) {
                $filters[$v['key']][] = $v['value'];
            } 
        }   
        return $filters;
    }

    /**
     * Getter for query
     *    
     * @return string
     */             
    public function getQuery()
    {   
        if (!$this->query) {
            $this->make();
        }
        return $this->query;
    }

    /**
     * Setter for index
     *
     * @param string $index
     * @return $this
     */
    public function setIndex($index)
    {
         $this->index = $index;
         return $this; 
    }

    /**
     * Getter for index
     *    
     * @return string $index
     */    
    public function getIndex()
    { 
        return $this->index;           
    }

    /**
     * Setter for type
     *
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
         $this->type = $type;
         return $this; 
    }

    /**
     * Getter for type
     *    
     * @return string
     */     
    public function getType()
    { 
        return $this->type;           
    }

    /**
     * Setter for option
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function setOption($key, $value)
    { 
        $this->options[$key] = $value;
        return $this;           
    }

    /**
     * Setter for options
     *
     * @param $options
     * @return $this
     * @internal param options $terms
     */
    public function setOptions($options)
    { 
        $this->options = Arr::parseOptions($this->options, $options);
        return $this;           
    }

    /**
     * Getter for options
     *    
     * @return array
     */     
    public function getOptions()
    { 
        return $this->options;           
    }

    /**
     * Setter for terms
     *
     * @param $terms array
     * @return $this
     */
    public function setTerms($terms)
    {
         $this->terms = $terms;
         return $this; 
    }

    /**
     * Getter for terms
     *    
     * @return array
     */     
    public function getTerms()
    { 
        return $this->terms;           
    }

    /**
     * Setter for facets
     *
     * @param $facets array
     * @return $this
     */
    public function setFacets($facets)
    {
         $this->facets = $facets;
         return $this; 
    }

    /**
     * Getter for facets
     *    
     * @return array
     */     
    public function getFacets()
    { 
        return $this->facets;           
    }

    /**
     * Setter for tags
     *
     * @param $tags array
     * @return $this
     */
    public function setTags($tags)
    {
         $this->tags = $tags;
         return $this; 
    }

    /**
     * Getter for tags
     *    
     * @return array
     */     
    public function getTags()
    { 
        return $this->tags;           
    } 
}