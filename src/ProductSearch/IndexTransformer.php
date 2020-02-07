<?php namespace Core\ProductSearch;

use App\Modules\Currency;
use App\Http\Resources\ProductResource;

class IndexTransformer
{ 
    public function transform($object)
    {   
        $currency = new Currency($object->price->currency_code);
        
        return [
            'product_id'        => $object->id,
            'platform_id'       => $object->platform_id,
            'product_group_id'  => $object->reference->product_group_id,
            'category_ids'      => collect($object->categories)->transform(function ($item) {
                                    return $item->id;
                                }),
            'sku'               => isset($object->format->identity->sku) ? $object->format->identity->sku : '',

            'name'              => $object->format->name, 
            'retail_price'      => $currency->fromCents($object->price->retail_price),
            'wholesale_price'   => $currency->fromCents($object->price->wholesale_price),
            
            'store_id'          => $object->store->id,
            'store_name'        => $object->store->name,
            'store_handle'      => $object->store->handle,
            'country_code'      => $object->store->country_code,
            
            'site_id'           => $object->site->id,
            'updated_at'        => isset($object->updated_at) ? (string) $object->updated_at : now(),
            //'updated_at'        => now(),

            'average_rating'    => $object->reference->average_rating,
            'number_ratings'    => $object->reference->number_ratings,

            'content'           => json_encode(new ProductResource($object))

            // 'manufacturer'      => $object->reference->manufacturer,
            // 'brand'             => $object->reference->brand,
            //'creator'               => $reference->creator,
           
            //'publisher_reference'   => $reference->publisher_reference,
            
            // 'category_name'         => $reference->category->name,
            // 'country'               => $site->country->name,            
            // 'categories'            => collect($object->categories)->transform(function ($item) {
            //                                 return $item->id;
            //                         }),

            //  put a lot of text into search
            //'descriptions'          => json_encode($reference->descriptions),
            //'features'              => json_encode($reference->features),
            // 'details'               => [
            //                             'Manufacturer',
            //                             'Author',
            //                             'Brand'
            //                         ],
        ];
    }
}