<?php namespace Core\CustomerSearch;

use App\Modules\Currency;
use App\Http\Resources\ProductResource;

class IndexTransformer
{ 
    protected $api_locale;
    protected $api_fallback_locale;
    
    public function transform($object)
    {   
        $currency = new Currency($object->price->currency_code);
        
        return [
        ];
    }    
}