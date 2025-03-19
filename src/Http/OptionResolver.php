<?php namespace Core\Http;

use Core\Http\RequestOptions;

class OptionResolver
{
    //  shorthand sorts
    const SORT_NEWEST       = 'newest';
    const SORT_OLDEST       = 'oldest';    
    const SORT_AZ           = 'az'; 
    const SORT_ZA           = 'za';
    const SORT_COUNTRY      = 'country';
    const SORT_RATING_DESC  = 'rating_desc';
    const SORT_RATING_ASC   = 'rating_asc';

    const DIRECTION_ASC     = 'asc';
    const DIRECTION_DESC    = 'desc';
    const ALLOWED_DIRECTIONS = [
        self::DIRECTION_ASC,
        self::DIRECTION_DESC
    ];

    const DEFAULT_PER_PAGE  = 500;
    const DEFAULT_SEARCH_STRING = ' * ';

    public function handle(
        $input,
        $defaults = [],
        $allowed_columns = [],
        $default_locale = ''
    )
    {
        if (!isset($defaults['per_page']) || empty($defaults['per_page'])) {
            $defaults['per_page'] = 25;
        }
   
        $options = new RequestOptions();
        $options = $this->makeSort($input, $options, $defaults, $allowed_columns);

        if ($input->get('limit')) {
            $per_page = $input->get('limit');
        } elseif ($input->get('per_page')) {
            $per_page = $input->get('per_page');
        } else {
            $per_page = $defaults['per_page'];
        }
        $options->setPerPage($per_page);
        
        $options->setPage($input->get('page', 1));
        $options->setIds((array) $input->get('ids'));
        
        $options->setFilters(
            $input->only($allowed_columns)->toArray()
        ); 
        
        if ($input->get('index')) {
            $options->setIndex($input->get('index'));
        }
        
        if ($input->get('q')) {
            $options->setSearchString(
                sanitizeString($input->get('q', self::DEFAULT_SEARCH_STRING))
            ); 
        }
        
        $options->setRawSearchString($input->get('q', '')); 
        $options->setLocale($input->get('locale', $default_locale));
        
        return $options;
    }
    
    public function makeSort($input, $options, $defaults, $allowed_columns)
    {     
        $sort = $input->get('sort') ? strtolower($input->get('sort')) : null;
        
        if ($shorthand = $this->matchShorthand($sort)) {         
            $options->setSortColumn($shorthand['column']);
            $options->setSortDirection($shorthand['direction']); 
        
        } elseif ($input->get('sort_column') && $input->get('sort_direction')) {
            $options->setSortColumn(strtolower($input->get('sort_column')));
            $options->setSortDirection(strtolower($input->get('sort_direction'))); 

        } else {
            if (isset($defaults['sort_column'])) {
                $options->setSortColumn($defaults['sort_column']);
            } 
            if (isset($defaults['sort_direction'])) {
                $options->setSortDirection($defaults['sort_direction']); 
            }    
        }            
        
        if (false == $options->getSortColumn() 
            || !in_array($options->getSortColumn(), $allowed_columns)) {
            $options->setSortColumn($defaults['sort_column']);
        }
        if (false == $options->getSortDirection() 
            || !in_array($options->getSortDirection(), self::ALLOWED_DIRECTIONS)) {   
            $options->setSortDirection($defaults['sort_direction']);
        }
        return $options;
     }   
     
     public function matchShorthand($sort = '')
     {      
        if ($sort) {
            foreach ($this->shorthands() as $shorthand) {
                if ($shorthand['slug'] == $sort) {
                    return $shorthand;
                }
            }
        }    
        return false;
     }

     public function shorthands()
     {
        return [
            [
                'slug' => self::SORT_NEWEST,
                'column' => 'updated_at',
                'direction' => self::DIRECTION_DESC
            ], 
            [
                'slug' => self::SORT_OLDEST,
                'column' => 'updated_at',
                'direction' => self::DIRECTION_ASC
            ],
            [
                'slug' => self::SORT_AZ,
                'column' => 'name',
                'direction' => self::DIRECTION_ASC
            ],
            [
                'slug' => self::SORT_ZA,
                'column' => 'name',
                'direction' => self::DIRECTION_DESC
            ],
            [
                'slug' => self::SORT_RATING_DESC,
                'column' => 'average_rating',
                'direction' => self::DIRECTION_DESC
            ],
            [
                'slug' => self::SORT_RATING_ASC,
                'column' => 'average_rating',
                'direction' => self::DIRECTION_ASC
            ],
            [
                'slug' => self::SORT_COUNTRY,
                'column' => 'average_rating',
                'direction' => self::DIRECTION_ASC
            ],                                                                                  
        ];
     }
}
