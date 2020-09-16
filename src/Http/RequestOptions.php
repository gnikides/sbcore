<?php namespace Core\Http;

class RequestOptions
{    
    private $index;
    private $page               = 1;
    private $per_page           = 25;
    private $is_paged           = true;
    private $search_string      = ' * ';
    private $raw_search_string  = '';    
    private $search_fields      = [ 'search_text' ];
    private $max_facets         = 15; // the max number of buckets returned for one facet
    private $min_doc_count      = 1; // min number of documents for a facet to be displayed          
    private $sort_column        = 'updated_at';
    private $sort_direction     = 'desc';
    private $filters;
    private $ids                = [];
    private $platform;
    private $locale             = 'default';
    private $language           = 'en';

    public function getIndex()
    {
        return $this->index;
    }

    public function setIndex(string $index)
    {
        $this->index = $index;
        return $this;
    }

    public function getIsPaged()
    {
        return $this->is_paged;
    }

    public function setIsPaged(bool $is_paged)
    {
        $this->is_paged = $is_paged;
        return $this;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setPage(int $page = 1)
    {
        $this->page = $page;
        return $this;
    }

    public function getPerPage()
    {
        return $this->per_page;
    }

    public function setPerPage(int $per_page)
    {
        $this->per_page = $per_page;
        return $this;
    }

    public function getSearchString()
    {
        return $this->search_string;
    }

    public function setSearchString(string $string)
    {
        $this->search_string = $string;
        return $this;
    }

    public function getRawSearchString()
    {
        return $this->raw_search_string;
    }

    public function setRawSearchString(string $string)
    {
        $this->raw_search_string = $string;
        return $this;
    }

    public function getSearchFields()
    {
        return $this->search_fields;
    }

    public function setSearchFields(array $array = [])
    {
        $this->search_fields = $array;
        return $this;
    }

    public function getMaxFacets()
    {
        return $this->max_facets;
    }

    public function setMaxFacets(int $max_facets)
    {
        $this->max_facets = $max_facets;
        return $this;
    }

    public function getMinDocCount()
    {
        return $this->min_doc_count;
    }

    public function setMinDocCount(int $min_doc_count)
    {
        $this->min_doc_count = $min_doc_count;
        return $this;
    }

    public function getSortColumn()
    {
        return $this->sort_column;
    }

    public function setSortColumn(string $sort_column)
    {
        $this->sort_column = $sort_column;
        return $this;
    }

    public function getSortDirection()
    {
        return $this->sort_direction;
    }

    public function setSortDirection(string $sort_direction)
    {
        $this->sort_direction = $sort_direction;
        return $this;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function setFilter(string $name, $value)
    {
        $this->filters[$name] = $value;
        return $this;
    }

    public function getFilter($name)
    {
        return $this->filters[$name];
    }

    public function setFilters(array $filters = [])
    {
        $this->filters = $filters;
        return $this;
    }

    public function getIds()
    {
        return $this->ids;
    }

    public function setIds(array $ids = [])
    {
        $this->ids = $ids;
        return $this;
    }
    
    public function getPlatform()
    {
        return $this->platform;
    }

    public function setPlatform(string $platform = null)
    {
        $this->platform = $platform;
        return $this;
    } 

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale(string $locale = null)
    {
        $this->locale = $locale;
        return $this;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setLanguage(string $language = null)
    {
        $this->language = $language;
        return $this;
    }    
}
