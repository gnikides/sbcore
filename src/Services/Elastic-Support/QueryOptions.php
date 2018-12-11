<?php namespace App\Support\Elastic;

class QueryOptions
{
    private $page           = 1;
    private $per_page       = 25;
    private $sort_column    = 'updated_at';
    private $sort_direction = 'desc';
    private $filters        = [];
    private $ids;

    public function getPage()
    {
        return $this->page;
    }

    public function setPage($page = 1)
    {
        $this->page = $page;
        return $this;
    }

    public function getPerPage()
    {
        return $this->per_page;
    }

    public function setPerPage($per_page)
    {
        $this->per_page = $per_page;
        return $this;
    }

    public function getSortColumn()
    {
        return $this->sort_column;
    }

    public function setSortColumn($sort_column)
    {
        $this->sort_column = $sort_column;
        return $this;
    }

    public function getSortDirection()
    {
        return $this->sort_direction;
    }

    public function setSortDirection($sort_direction)
    {
        $this->sort_direction = $sort_direction;
        return $this;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function setFilters($filters)
    {
        $this->filters = $filters;
        return $this;
    }

    public function getIds()
    {
        return $this->ids;
    }

    public function setIds($ids)
    {
        $this->ids = $ids;
        return $this;
    }
}