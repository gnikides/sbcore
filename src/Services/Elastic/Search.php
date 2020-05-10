<?php namespace Core\Services\Elastic;

class Search
{
    private $client;
    private $results = [];
    private $items = [];
    private $facets = [];
    private $count = 0;

    public function __construct($client = '')
    {
        if (!$client) {
            $this->client = \Elasticsearch\ClientBuilder::create()
            ->setHosts([ config('services.elastic.host').':'.config('services.elastic.port') ])
            ->build();
        } else {
            $this->client = $client;
        } 
    }

    public function search($query)
    {
        $this->results = $this->client->search($query);
        $this->items = $this->results['hits']['hits'];
        $this->count = $this->results['hits']['total'];
        if (array_key_exists('aggregations', $this->results)) { 
            $this->facets = $this->results['aggregations'];
        }
        return $this;
    }
                  
    public function getResults()
    {
        return $this->results;
    }

    public function getItems()
    {
        return $this->items;
    }
    
    public function getCount()
    {
        return $this->count;
    }

    public function getFacets()
    {
        return $this->facets;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }
}