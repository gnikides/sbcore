<?php namespace App\Support;

// 
// $cust = new Cust();
// $cust->get($id);
// $cust->create([]);
// $cust->update([]);
// $cust->delete(1);
// $cust->billingAddress();


class Model
{
    protected $endpoint;
    protected $attributes = [];
    protected $original_data;
    protected $is_cache;
        
    public function __construct($attributes = [], $is_cache = true)
    {
        $this->setAttributes($attributes, $is_cache);
        $this->init();
    }

    public function setAttributes(array $attributes = [], bool $is_cache = true)
    {
        $this->attributes       = collect($attributes);
        $this->original_data    = $attributes;
        $this->is_cache         = $is_cache;
        return $this;
    }

    public function init()
    {
    }

    public function get($id, $is_cache)
    {
        $this->attributes = collect(api($this->endpoint)->get($id, $is_cache));
    }

    public function create(array $params = [])
    {
    }

    public function update(array $params = [])
    {
    }

    public function delete($id, $is_cache)
    {
    }

    protected function value($key)
    {
        return $this->attributes->value($key);
    }

    public function id()
    {
        return $this->value('id');
    }
   
    public function shard()
    {
        return $this->value('shard');
    }
  
    public function status()
    {
        return $this->value('status');
    }
        
    public function metadata()
    {
        return $this->value('metadata');
    }

    public function updatedAt()
    {
        return $this->value('updated_at');
    }

    public function createdAt()
    {
        return $this->value('created_at');
    }

    public function updatedAtTimeAgo()
    {
        return timeAgo($this->updatedAt());
    }

    public function createdAtTimeAgo()
    {
        return timeAgo($this->createdAt());
    }
    
    public function getattributes()
    {
        return $this->attributes;
    }

    public function all()
    {
        return $this->attributes->all();
    }
        
    public function reset()
    {
        $this->attributes = null;
    }
    
    public function count()
    {
        return $this->attributes->count();
    }
    
    public function has($key)
    {
        return $this->attributes->has($key);
    }

    public function contains($value)
    {
        return $this->attributes->contains($value);
    }

    public function search($query)
    {
        return $this->attributes->search($query);
    }

    public function originalData()
    {
        return $this->original_data;
    }

    public function dd()
    {
        return $this->attributes->dd();
    }
}
