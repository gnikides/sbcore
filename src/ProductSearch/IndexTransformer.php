<?php namespace Core\ProductSearch;

use App\Support\Http\Transformer as BaseTransformer;
use App\Modules\Currency;
use App\Http\Transformers\ProductSearchTransformer;

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
        $s = $this->filter([
            'product_id'            => $object->id,
            'name'                  => $name,
            'manufacturer'          => $reference->manufacturer,
            'brand'                 => $reference->brand,
            'creator'               => $reference->creator,
            'sku'                   => $reference->sku,
            'publisher_reference'   => $reference->publisher_reference,
            'product_group_id'      => $reference->product_group_id,
            // 'category_id'           => $reference->category_id,
            // 'category_name'         => $reference->category->name,
            'updated_at'            => isset($version->updated_at) ? $this->transformDate($version->updated_at) : now(),
            'retail_price'          => $currency->fromCents($price->retail_price),
            'wholesale_price'       => $currency->fromCents($price->wholesale_price),
            'store_id'              => $store->id,
            'store_name'            => $store->name,
            'store_handle'          => $store->handle,
            'country_code'          => $store->country_code,
            // 'country'               => $site->country->name,
            'average_rating'        => $reference->average_rating,
            'number_ratings'        => $reference->number_ratings,
            'categories'            => collect($object->categories)->transform(function ($item) {
                                            return $item->id;
                                    }),

            //  @todo
            //  put a lot of text into search
            //'descriptions'          => json_encode($reference->descriptions),
            //'features'              => json_encode($reference->features),
            // 'details'               => [
            //                             'Manufacturer',
            //                             'Author',
            //                             'Brand'
            //                         ],
            'content'               => json_encode((new ProductSearchTransformer)->transform($object))
        ]);
        // sb($s);
        // exit();
        return $s;
    }
}
