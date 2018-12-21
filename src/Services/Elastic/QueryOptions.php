<?php namespace Core\Services\Elastic;

class QueryOptions
{    
    private $page           = 1;
    private $per_page       = 25;
    private $sort_column    = 'updated_at';
    private $sort_direction = 'desc';
    private $filters        = [];
    private $ids            = [];
    private $platform;
    private $language       = 'en';
    
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
}
