<?php namespace Core\Http;

use Core\Http\Resource as JsonResource;
use Countable;
use IteratorAggregate;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Http\Resources\CollectsResources;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;

class ResourceCollection extends JsonResource implements Countable, IteratorAggregate
{
    use CollectsResources;

    public $collects;
    public $collection;
    protected $expandable = [];
    protected $request_locale = 'default';
    protected $fallback_locale = 'default';
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    public function __construct($resource, $request_locale = 'default')
    {   
        $this->request_locale = $request_locale; 
        parent::__construct($resource, $this->request_locale);
        $this->resource = $this->collectResource($resource);
    }

    /**
     * Return the count of items in the resource collection.
     *
     * @return int
     */
    public function count()
    {
        return $this->collection->count();
    }

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->collection->map->setRequestLocale($this->request_locale);
        return $this->collection->map->toArray($request)->all();
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return $this->resource instanceof AbstractPaginator
                    ? (new PaginatedResourceResponse($this))->toResponse($request)
                    : parent::toResponse($request);
    }

    public function resolveTranslation($values, $locale = '')
    {   
        if (!$values) {
            return null;
        } elseif (!is_array($values)) {
            return $values;
        }
        $locale = $locale ? $locale : $this->request_locale; 
        return array_key_exists($locale, $values) ? $values[$locale] : null;
    }        
}