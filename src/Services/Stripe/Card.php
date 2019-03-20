<?php namespace Core\Services\Stripe;

use Api\Resources\PayMethod;

class Card
{ 
    public function add(
        string $token,
        string $email,
        bool $is_default = false,      
        $provider_customer_id = ''
    )
    {               
        try {        
            if ($provider_customer_id) {
                return $this->update($provider_customer_id, $token, $is_default);              
            }       
            return $this->store($token, $email, $is_default);            
        } catch (\Exception $e) {
            \Log::error('Api exception', [$e]);         
        }           
    }

    public function store(string $token, string $email, bool $is_default = true)
    {             
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));                       
            $customer = \Stripe\Customer::create([
                'email' => time() . $email,
                'source' => $token
            ]);            
            //  @todo get errors from stripe            
            if ($customer) {            
                $card = $customer['sources']['data'][0];                
                return [
                    'card_id'               => $card['id'],
                    'provider_customer_id'  => $customer['id'],
                    'brand'                 => $card['brand'],
                    'last_four'             => $card['last4'],
                    'expiry_month'          => $card['exp_month'],
                    'expiry_year'           => $card['exp_year'],
                    'card_name'             => $card['name'],
                    'country_code'          => $card['country'],
                    //'is_default'            => $is_default
                ];
            }   
            return false;            
        } catch (\Exception $e) {
            \Log::error('Api exception', [$e]);         
        }           
    }
    
    public function update(
        $provider_customer_id,
        string $token,
        bool $is_default = true
    )
    {   
        try {                   
            $paymethod_id = sanitizeString($this->request->input('paymethod_id'));
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));                               
            $customer = \Stripe\Customer::retrieve($provider_customer_id);
            $customer->source = $token;
            $customer->save();            
            //  @todo get errors from stripe            
            if ($customer) {                
                $card = $customer['sources']['data'][0];                
                return [
                    'card_id'               => $card['id'],
                    'provider_customer_id'  => $customer['id'],
                    'brand'                 => $card['brand'],
                    'last_four'             => $card['last4'],
                    'expiry_month'          => $card['exp_month'],
                    'expiry_year'           => $card['exp_year'],
                    'card_name'             => $card['name'],
                    'country_code'          => $card['country'],
                    'is_default'            => $is_default
                ];                
            }   
            return false;
        } catch (\Exception $e) {
            \Log::error('Api exception', [$e]);         
        }           
    } 

    public function saveCustomerCreditCard(array $params = [], int $paymethod_id = null, bool $is_default = false)
    {   
        $paymethod = ($paymethod_id) ? PayMethod::update($paymethod_id, $params) : PayMethod::create($params);
        if ($paymethod) {
            if ($is_default) {
                session(['paymethod_id' => $paymethod->id()]);
            }
            Refresh::customerPayMethods($params['customer_id']);
            return true;
        } 
        return false;                    
    } 
}   