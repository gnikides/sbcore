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
        $site = $object->site;
        $price = $object->price;
        $currency = new Currency($price->currency_code);
        return $this->filter([
            'product_id'            => $object->id,
            //'version_id'            => (int) $object->id,
            //'format_id'             => (int) $object->format->id,
            'product_name'          => $reference->name,
            'manufacturer'          => $reference->manufacturer,
            'creator'               => $reference->creator,
            'sku'                   => $reference->sku,
            'publisher_reference'   => $reference->publisher_reference,
            'category_id'           => $reference->category_id,
            'category_name'         => $reference->category->name,
            'updated_at'            => isset($version->updated_at) ? $this->transformDate($version->updated_at) : now(),
            'gross_price'           => $currency->fromCents($price->gross_price),
            'net_price'             => $currency->fromCents($price->net_price),
            'site_id'               => $site->id,
            'site_name'             => $site->name,
            'handle'                => $site->handle,
            'country_code'          => $site->country_code,
            'country'               => $site->country->name,
            'average_rating'        => $reference->average_rating,
            'number_ratings'        => $reference->number_ratings,
            'attributes'            => json_encode($reference->reference_attributes),
            'descriptions'          => json_encode($reference->descriptions),
            'features'              => json_encode($reference->features),
            'content'               => json_encode((new ProductTransformer)->transform($object))
        ]);
    }
}