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

    const ASC               = 'asc';
    const DESC              = 'desc';
    const ALLOWED_DIRECTIONS = [
        self::ASC,
        self::DESC
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
        $options->setPerPage($input->get('limit', $defaults['per_page']));
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
        $sort = $input->get('sort'); 
        
        if ($shorthand = $this->matchShorthand($sort)) {          
            $options->setSortColumn($shorthand['column']);
            $options->setSortDirection($shorthand['direction']); 
        
        } elseif ($input->get('sort_column') && $input->get('sort_direction')) {
            $options->setSortColumn($input->get('sort_column'));
            $options->setSortDirection($input->get('sort_direction')); 
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
     
     public function matchShorthand(string $sort)
     {      
        foreach ($this->shorthands() as $shorthand) {
            if ($shorthand['slug'] == $sort) {
                return $shorthand;
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
                'direction' => self::DESC
            ], 
            [
                'slug' => self::SORT_OLDEST,
                'column' => 'updated_at',
                'direction' => self::ASC
            ],
            [
                'slug' => self::SORT_AZ,
                'column' => 'name',
                'direction' => self::ASC
            ],
            [
                'slug' => self::SORT_ZA,
                'column' => 'name',
                'direction' => self::DESC
            ],
            [
                'slug' => self::SORT_RATING_DESC,
                'column' => 'average_rating',
                'direction' => self::DESC
            ],
            [
                'slug' => self::SORT_RATING_ASC,
                'column' => 'average_rating',
                'direction' => self::ASC
            ],
            [
                'slug' => self::SORT_COUNTRY,
                'column' => 'average_rating',
                'direction' => self::ASC
            ],                                                                                  
        ];
     }
}
