<?php namespace Core\ProductSearch;

use Core\Http\Resource;
use App\Modules\Currency;
use App\Http\Resources\ProductResource;

class IndexResource extends Resource
{ 
    protected $expandable = [
        //
    ];

    public function toArray($request)
    {   
        //lg($this->resource->toArray());
        $currency = new Currency($this->price->currency_code);
        
        return [
            'product_id'        => $this->id,
            'platform_id'       => $this->platform_id,
            'product_group_id'  => $this->reference->product_group_id,
            'category_ids'      => collect($this->categories)->transform(function ($item) {
                                    return $item->id;
                                }),
            'sku'               => $this->format->idenity->sku,

            'name'              => $this->format->name, 
            'retail_price'      => $currency->fromCents($this->price->retail_price),
            'wholesale_price'   => $currency->fromCents($this->price->wholesale_price),
            
            'store_id'          => $this->store->id,
            'store_name'        => $this->store->name,
            'store_handle'      => $this->store->handle,
            'country_code'      => $this->store->country_code,
            
            'site_id'           => $this->site->id,
            'updated_at'        => isset($this->updated_at) ? (string) $this->updated_at : now(),
                        
            'average_rating'    => $this->reference->average_rating,
            'number_ratings'    => $this->reference->number_ratings,

            'content'           => json_encode(new ProductResource($this->resource))

            // 'manufacturer'      => $this->reference->manufacturer,
            // 'brand'             => $this->reference->brand,
            //'creator'               => $reference->creator,
           
            //'publisher_reference'   => $reference->publisher_reference,
            
            // 'category_name'         => $reference->category->name,
            // 'country'               => $site->country->name,            
            // 'categories'            => collect($this->categories)->transform(function ($item) {
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