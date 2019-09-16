<?php namespace Core\ProductSearch;

use App\Support\Http\Transformer as BaseTransformer;
use App\Modules\Currency;
use App\Http\Transformers\ProductTransformer;

class IndexTransformer extends BaseTransformer
{
    protected $expandable = [
        //
    ];
    
    public function transform($object)
    {   
        $reference = $object->reference;
        $format = $object->format;
        $version = $object->version;
        $store = $object->store;
        $price = $object->price;
        $currency = new Currency($price->currency_code);
        
        //sb($version);

        $name = '';
        //sb($format->props);
        if ($format && isset($format->props)) {
            $name = isset($format->props['default']['name']['value']) ? $format->props['default']['name']['value'] : '';
            
            // foreach ($format->props as $prop) {
            //     if ($prop['name'] == 'name') {
            //         $name = $prop['value'];
            //     }
            // }    
        } 
        // sb($name);
        // exit();
        $index = $this->filter([
            'product_id'            => $object->id,
            //'version_id'            => (int) $object->id,
            //'format_id'             => (int) $object->format->id,
            'product_name'          => $name,
            'manufacturer'          => $reference->manufacturer,
            'creator'               => $reference->creator,
            'sku'                   => $reference->sku,
            'publisher_reference'   => $reference->publisher_reference,
            // 'category_id'           => $reference->category_id,
            // 'category_name'         => $reference->category->name,
            'updated_at'            => isset($version->updated_at) ? $this->transformDate($version->updated_at) : now(),
            'retail_price'          => $currency->fromCents($price->retail_price),
            'wholesale_price'       => $currency->fromCents($price->wholesale_price),
            'store_id'              => $store->id,
            'store_name'            => $store->name,
            'handle'                => $store->handle,
            'country_code'          => $store->country_code,
            // 'country'               => $site->country->name,
            'average_rating'        => $reference->average_rating,
            'number_ratings'        => $reference->number_ratings,
            // 'props'                 => json_encode($reference->properties),
            //'descriptions'          => json_encode($reference->descriptions),
            //'features'              => json_encode($reference->features),
            'content'               => json_encode((new ProductTransformer)->transform($object))
        ]);
        //sb($index);
        //\Log::info('index', [ $index  ] ); 
        return $index;
    }
}
