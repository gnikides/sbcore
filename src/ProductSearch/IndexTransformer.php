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
        $version = $object->version;
        $reference = $object->reference;
        $name = is_object($reference->name) ? current((Array)$reference->name) : $reference->name;
        $store = $object->store;
        $price = $object->price;
        $currency = new Currency($price->currency_code);
       
        // sb($object->version);
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
            'category_id'           => $reference->category_id,
            'category_name'         => $reference->category->name,
            'updated_at'            => isset($version->updated_at) ? $this->transformDate($version->updated_at) : now(),
            'gross_price'           => $currency->fromCents($price->gross_price),
            'net_price'             => $currency->fromCents($price->net_price),
            'store_id'              => $store->id,
            'store_name'            => $store->name,
            'handle'                => $store->handle,
            'country_code'          => $store->country_code,
            // 'country'               => $site->country->name,
            'average_rating'        => $reference->average_rating,
            'number_ratings'        => $reference->number_ratings,
            'properties'            => json_encode($reference->properties),
            //'descriptions'          => json_encode($reference->descriptions),
            //'features'              => json_encode($reference->features),
            'content'               => json_encode((new ProductTransformer)->transform($object))
        ]);
        //\Log::info('index', [ $index  ] ); 
        return $index;
    }
}
