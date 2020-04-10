<?php namespace Core\Search\Customer;

use App\Http\Resources\CustomerResource;
use App\Models\OrderComponent;

class IndexTransformer
{     
    public function transform($object)
    {   
        return [
            'id'                => $object->id,
            'customer_group_id' => $object->customer_group_id,
            'full_name'         => trim($object->first_name . ' ' . $object->last_name),
            'email'             => $object->email,
            'country_code'      => $object->country->code,
            'country_name'      => $object->country->name,
            'updated_at'        => (string) $object->updated_at,     
            'ip'                => $object->ip,
            'status'            => $object->status,
            'store_ids'         => OrderComponent::where('customer_id', $object->id)->pluck('store_id')->unique()->values()->toArray(),                                                        
            'content'           => json_encode(new CustomerResource($object))             
        ];
    }
}