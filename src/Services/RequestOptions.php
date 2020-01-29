<?php namespace Core\Services;

class RequestOptions
{    
    private $index;
    private $page           = 1;
    private $per_page       = 25;
    private $search_string  = ' * ';
    private $sort_column    = 'updated_at';
    private $sort_direction = 'desc';
    private $filters;
    private $ids            = [];
    private $platform;
    private $language       = 'en';
    private $is_paged        = true;

    public function getIndex()
    {
        return $this->index;
    }

    public function setIndex(string $index)
    {
        $this->index = $index;
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

    public function setPlatform(string $platform)
    {
        $this->platform = $platform;
        return $this;
    } 
    
    public function getLanguage()
    {
        return $this->language;
    }

    public function setLanguage(string $language)
    {
        $this->language = $language;
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
}
