<?php namespace Core\Http;

use Illuminate\Http\Resources\Json\ResourceCollection as BaseCollection;

class ResourceCollection extends BaseCollection
{
    protected $expandable = [];
    protected $api_locale = 'default';
    protected $fallback_locale = 'default';
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    public function __construct($resource, $options = null)
    {   
        if (is_object($options)) {
            $this->api_locale = $options->getLocale();
        }   
        parent::__construct($resource);
    }
    
    public function resolveTranslation($values, $locale = '')
    {   
        if (is_string($values)) {
            return $values;
        }
        $locale = $locale ? $locale : $this->api_locale;
        $locale = $locale ? $locale : $this->fallback_locale;    
        return array_key_exists($locale, $values) ? $values[$locale] : null;
    }    
}