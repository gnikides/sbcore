<?php namespace App\Http\Transformers;

use App\Support\Http\Transformer;

class ProductTransformer extends Transformer
{
    protected $expandable = [
        //
    ];
    
    public function transform($object)
    {
        $output = [
            'id'                => (int) $object->id,
            
            'platform_id'       => (int) $object->platform_id,
            'slug'              => $object->slug,
            'status'            => $object->status,
            'average_rating'    => isset($object->reference->average_rating) ? $object->reference->average_rating : 0,
            'number_ratings'    => isset($object->reference->number_ratings) ? $object->reference->number_ratings : 0, 
            'updated_at'    => $this->transformDate($object->updated_at),
            'meta'          => $object->meta,
            'reference'     => (new ReferenceTransformer)->setExpandable()->transform($object->reference),
            'format'        => (new FormatTransformer)->setExpandable()->transform($object->format),
            'price'         => (new PriceTransformer)->setExpandable()->transform($object->price),
            'version'       => (new VersionTransformer)->setExpandable()->fields([
                                'name','slug','full_name'
                            ])->transform($object->version),
            'categories'    => collect($object->categories)->transform(function ($item) {
                                return (new CategoryTransformer)->fields([
                                    'name','slug', 'full_name'
                                    ])->transform($item);
                                })->toArray(),   
            'product_group' => (new ProductGroupTransformer)->setExpandable()->fields([
                                'name','slug','full_name'
                            ])->transform($object->product_group),                  
            'store'         => (new StoreTransformer)->setExpandable()->fields([
                                'id','name','handle'
                            ])->transform($object->store)
        ];
        return $this->filter($output);
    }
}