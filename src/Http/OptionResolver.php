<?php namespace Core\Http;

use Core\Http\RequestOptions;

class OptionResolver
{
    //  shorthand sorts
    const SORT_NEWEST = 'newest';
    const SORT_OLDEST = 'oldest';    
    const SORT_AZ = 'az'; 
    const SORT_ZA = 'za';
    const SORT_COUNTRY = 'country';
    const SORT_STARS_HIGHEST = 'stars_highest';
    const SORT_STARS_LOWEST = 'stars_lowest';
    const ALLOWED_FILTERS = [
        'status'
    ];
    const DEFAULT_PER_PAGE = 500;
    const DEFAULT_SEARCH_STRING = ' * ';
    protected $model;

    public function handle($input, $defaults = [], \App\Support\Eloquent\Model $model = null)
    {
        $this->model = $model;     
        $options = new RequestOptions();
        $options = $this->makeSort($input, $options, $defaults);
        $options->setPerPage($input->get('limit', $defaults['per_page']));
        $options->setPage($input->get('page', 1));
        $options->setIds((array) $input->get('ids'));
        $options->setFilters($this->makeFilters($input, $defaults)); 
        if ($input->get('index')) {
            $options->setIndex($input->get('index'));
        }
        if ($input->get('q')) {
            $options->setSearchString(sanitizeString($input->get('q', self::DEFAULT_SEARCH_STRING))); 
        }
        $options->setLocale($input->get('locale', 'default'));
        return $options;
    }

    public function makeFilters($input, $defaults)
    {
        if (!array_key_exists('allowed_filters', $defaults)) {
            $defaults['allowed_filters'] = [];
        }       
        return $input->only($defaults['allowed_filters'])->toArray();
    }
    
    public function makeSort($input, $options, $defaults)
    {
        $shorthands[self::SORT_NEWEST] = [
            'column' => 'updated_at',
            'direction' => 'desc'
        ];
        $shorthands[self::SORT_OLDEST] = [
            'column' => 'updated_at',
            'direction' => 'asc'
        ]; 
        $shorthands[self::SORT_AZ] = [
            'column' => 'name',
            'direction' => 'asc'
        ];
        $shorthands[self::SORT_ZA] = [
            'column' => 'name',
            'direction' => 'desc'
        ];
        $shorthands[self::SORT_STARS_HIGHEST] = [
            'column' => 'average_rating',
            'direction' => 'desc'
        ];
        $shorthands[self::SORT_STARS_LOWEST] = [
            'column' => 'average_rating',
            'direction' => 'asc'
        ];        
        $shorthands[self::SORT_COUNTRY] = [
            'column' => 'country_code',
            'direction' => 'asc'
        ];         
        $sort = $input->get('sort');      
        if ($sort && in_array($sort, array_keys($shorthands))) {            
            $options->setSortColumn($shorthands[$sort]['column']);
            $options->setSortDirection($shorthands[$sort]['direction']); 
        } else {        
             $column = $input->get('sort_column');
             $direction = $input->get('sort_direction'); 
             if ($column && $direction) {
                 $options->setSortColumn($column);
                 $options->setSortDirection($direction);
             }    
        }
        if (!array_key_exists('allowed_sorts', $defaults)) {
            $defaults['allowed_sorts'] = [ 'updated_at' ];
        }
        if (false == $options->getSortColumn() || !in_array($options->getSortColumn(), $defaults['allowed_sorts'])) {
            $options->setSortColumn($defaults['sort_column']);
            $options->setSortDirection($defaults['sort_direction']);
        }
        if ($this->model) {
            $json_fields = $this->model->getJsonFields();
            $translatable = $this->model->getTranslatable();
            if ($translatable && in_array($options->getSortColumn(), $translatable)) {
                if ($options->getLocale()) {  
                    $options->setSortColumn($json_fields[$options->getSortColumn()].'->'.$options->getLocale());             
                } else {   
                    $options->setSortColumn($json_fields[$options->getSortColumn()]);
                }
            }
        }
        return $options;
    }    
}



