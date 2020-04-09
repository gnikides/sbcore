<?php namespace Core\Search\Order;

use App\Modules\Currency;
use App\Http\Resources\OrderStoreResource;
use App\Http\Resources\AddressResource;
use App\Support\Resource\Totals;

class IndexTransformer
{ 
    protected $api_locale;
    protected $api_fallback_locale;
    


    public function transform($object)
    {    
        lg($object->order->shipping_address);       
        $history = isset($object->histories) && is_object($object->histories) ? $object->histories->shift() : null;
        $currency = new Currency($object->currency_code); 
        $totals = (new Totals($object->totals, $currency));                     
        return [
            'id'                => $object->id,
            'order_id'          => $object->order_id,  
            'store_id'          => $object->store_id,      
            'status'            => isset($history->action) ? $history->action : '',
            'pay_status'        => isset($history->pay_status) ? $history->pay_status : '',
            'ship_status'       => isset($history->ship_status) ? $history->ship_status : '', 
            'pay_brand'         => $object->order->paymethod ? $object->order->paymethod->brand : '',                          
            'total'             => $totals->getTotal(),
            'customer_id'       => $object->customer_id,                               
            'email'             => $object->customer->email,
            'full_name'         => $object->customer->full_name,
            'number_items'      => $this->getNumberItems($object->items),
            'country_code'      => $object->order->shipping_address->country_code,
            'country_name'      => '',
            'shipping_address'  => '', // isset($object->order->shipping_address) ? (new AddressResource($object->order->shipping_address, 'default'))->noExpands() : null,
            'product'           => '',
            'updated_at'        => (string) $object->updated_at,                     
            'content'           => json_encode(new OrderStoreResource($object))
        ];
    }

    public function getNumberItems($items)
    {
        $quantity = 0;
        foreach ($items as $item) {
            $quantity += $item->quantity;
        } 
        return $quantity;
    }    
}
