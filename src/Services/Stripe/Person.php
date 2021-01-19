<?php namespace Core\Services\Stripe;

use Core\Services\Stripe\Helper;

class Person
{ 
    public function create($account_id, array $payload, $prefix = 'representaitve')
    {  
        $params = $this->format($payload, $prefix);              
        $stripe = new \Stripe\StripeClient([
            "api_key" => config('services.stripe.secret'),
            "stripe_version" => config('services.stripe.version')            
        ]);
        $response = $stripe->accounts->createPerson($account_id, $params);    
        if ($response->id && empty($response->failure_code)) {            
            return $response;
        }
        \Log::error('stripe create person failed', [$payload, $response]);
        return false;
    }
    
    public function update($account_id, $person_id, $payload)
    {
        $stripe = new \Stripe\StripeClient([
            "api_key" => config('services.stripe.secret'),
            "stripe_version" => config('services.stripe.version')            
        ]);
        $account = $stripe->accounts->retrievePerson($account_id, $person_id);
        //  
        if ($account->save()) {
            return true; 
        }
        \Log::error('stripe update failed', [$account_id, @$account, $file]);
        return false; 
    }

    public function getFormat()
    {
        return [
            'first_name'        => 'first_name',
            'last_name'         => 'last_name',
            'title'             => 'relationship.title',         
            'email'             => 'email',
            'phone'             => 'phone',
            'metadata'          => 'metadata',
            'dob_day'           => 'dob.day',
            'dob_month'         => 'dob.month',
            'dob_year'          => 'dob.year',
            'address1'          => 'address.line1',
            'address2'          => 'address.line2',
            'city'              => 'address.city',
            'state'             => 'address.state',
            'postcode'          => 'address.postal_code',
            'country_code'      => 'address.country',
            'is_representative' => 'relationship.representative',  
            'is_owner'          => 'relationship.owner',  
            'is_executive'      => 'relationship.executive',  
            'is_director'       => 'relationship.director',
            'id_front'          => 'verification.document.front',  
            'id_back'           => 'verification.document.back',
            'additional_id'     => 'verification.additional_document.front'                                                               
        ];    
    }

    public function format(array $input, string $prefix = 'representative')
    {     
        $format = $this->getFormat();        
        $output = [];
        foreach ($format as $key => $val) {
            if ($prefix) {
                $key = $prefix.'_'.$key;
            }
            if (array_key_exists($key, $input) && $input[$key]) {
                $val = explode('.',$val);
                if (isset($val[2])) {
                    $output[$val[0]][$val[1]][$val[2]] = $input[$key];
                } elseif (isset($val[1])) {    
                    $output[$val[0]][$val[1]] = $input[$key];
                } elseif (isset($val[0])) {       
                    $output[$val[0]] = $input[$key];
                }    
            }
        }
        sb($output);
        return $output;
    }
}
