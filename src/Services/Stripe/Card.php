<?php namespace Core\Services\Stripe;

class Card
{     
    public function update(string $customer_key, string $token, bool $is_default = true)
    {   
        try {                   
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));                               
            $customer = \Stripe\Customer::retrieve($customer_key);
            $customer->source = $token;
            $customer->save();            
            //  @todo get errors from stripe    
            \Log::info('card1', [$customer]);           
            return $customer ? $this->transform($customer['sources']['data'][0], $customer['id'], $is_default) : false;  
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
            \Log::info('card2', [$customer]);             
            //  @todo get errors from stripe            
            return $customer ? $this->transform($customer['sources']['data'][0], $customer['id'], $is_default) : false;         
        } catch (\Exception $e) {
            \Log::error('Api exception', [$e]);         
        }           
    }

    public function transform($card, string $customer_key, bool $is_default = true)
    {
        return [
            'card_key'      => $card['id'],
            'customer_key'  => $customer_key,
            'brand'         => $card['brand'],
            'last_four'     => $card['last4'],
            'expiry_month'  => $card['exp_month'],
            'expiry_year'   => $card['exp_year'],
            'card_name'     => $card['name'],
            'country_code'  => $card['country'],
            'is_default'    => $is_default
        ];       
    }
}   
