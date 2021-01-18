<?php namespace Core\Services\Stripe;

class Person
{ 
    public function create(array $payload)
    {  
        $payload = $this->format($payload);              
        $stripe = new \Stripe\StripeClient([
            "api_key" => config('services.stripe.secret'),
            "stripe_version" => config('services.stripe.version')            
        ]);
        $response = $stripe->accounts->createPerson($payload);    
        if ($response->id && empty($response->failure_code)) {
            return $response;
        }
        \Log::error('stripe create person failed', [$payload, $response]);
        return false;
    }
    
    public function update($account_id, $person_id, $file = '')
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

            //  representative
            'representative_first_name'     => 'representative.first_name',
            'representative_last_name'      => 'representative.last_name',
            'representative_title'          => 'representative.title',          
            'representative_dob_day'        => 'representative.dob.day',
            'representative_dob_month'      => 'representative.dob.month',
            'representative_dob_year'       => 'representative.dob.year',
            'representative_email'          => 'representative.email',
            'representative_phone'          => 'representative.phone',
            'representative_metadata'       => 'representative.metadata',

            'representative_relationship'   => 'representative.relationship'
        ];    
    }

    public function format(array $input)
    {     
        $format = $this->getFormat();        
        $output = [];
        foreach ($format as $key => $val) {
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
        // sb($output);
        // exit();
        return $output;
    }
}
