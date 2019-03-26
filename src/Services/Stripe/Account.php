<?php namespace Core\Services\Stripe;

class Account
{ 
    public function create(array $payload)
    {  
        $payload = $this->formatPayload($payload);        
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $response = \Stripe\Account::create($payload);         
        if ($response['id'] && empty($response['failure_code'])) {
            return $response;
        }
        \Log::error('stripe create account failed', [$payload, $response]);
        return false;
    }
    
    public function update($account_id, $file = '')
    {
        /*  @gotcha Only by retrieving and saving were we able to set additional_owners to null
            If additional owners not null payouts can be disabled
            \Stripe\Account::update() did not work
        */
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));  
        $account = \Stripe\Account::retrieve($account_id);        
        if ($file) {
            $account->legal_entity->verification->document = $file;
        }
        //    !!don't change unless additional_owner needed
        $account->legal_entity->additional_owners = null;
        if ($account->save()) {
            return true; 
        }
        \Log::error('stripe update failed', [$account_id, @$account, $file]);
        return false; 
    }
    
    public function formatPayload(array $input)
    {     
        /* business address */
        $business_address = [
            'line1'             => $input['business_address1'],
            'line2'             => @$input['business_address2'],
            'city'              => $input['business_city'],
            'postal_code'       => $input['business_postcode']                    
        ];
        if ($input['business_state']) {
            $business_address['state']     = $input['business_state'];
        }

        /* representative address */            
        $personal_address = [
            'line1'             => $input['personal_address1'],
            'line2'             => @$input['personal_address2'],
            'city'              => $input['personal_city'],
            'postal_code'       => $input['personal_postcode']                    
        ]; 
        if ($input['personal_state']) {
            $personal_address['state']     = $input['personal_state'];
        }
        /* legal entity */                                        
        $legal_entity = [
            'type'                  => $input['business_type'],
            'business_name'         => $input['business_name'],
            'business_tax_id'       => $input['business_tax_id'],                                            
            'first_name'            => $input['first_name'],
            'last_name'             => $input['last_name'],
            'personal_id_number'    => $input['personal_id_number'],
            'address'               => $business_address,
            'dob'                   => [
                'day'               => $input['dob_day'],
                'month'             => $input['dob_month'],
                'year'              => $input['dob_year'],            
            ],
            'personal_address'      => $personal_address    
        ];        
        $tos_acceptance             = [
            'date'                  => $input['tos_acceptance_date'],
            'ip'                    => $input['tos_acceptance_ip']
        ];        
        $payload = [
            'type'                  => $input['account_type'],
            'country'               => $input['business_country_code'],
            'email'                 => $input['email'],        
            'default_currency'      => $input['default_currency'],
            'tos_acceptance'        => $tos_acceptance,    
            'legal_entity'          => $legal_entity                                                        
        ];

        // @gotcha json transforms true to 1 but stripe expects only true, false
        if ('1' == $input["debit_negative_balances"]) {
            $payload["debit_negative_balances"] = true;
        } elseif ('0' == $input["debit_negative_balances"]) {  
            $payload["debit_negative_balances"] = false;
        }
        return $payload;  
    }
    
    public function verify($account_id)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));         
        $account = \Stripe\Account::retrieve($account_id);  
        
        if (!empty($account->verification->due_by) ||
            !empty($account->verification->disabled_reason) ||       
            !empty($account->verification->fields_needed) ||
            empty($account->legal_entity->verification->document) ||
            'verified' != $account->legal_entity->verification->status) {
            
            \Mail::raw(json_encode($account), function($message) {
                $message->to(trim(config('mail.admin')));
                $message->subject('Merchant account needs verification');
            });
        }

        //  verify account can accept payments
        return (
            ($account->id == $account_id) &&
            ('account' == $account->object) &&
            (true == $account->charges_enabled) &&
            (true == $account->details_submitted) &&
            (!empty($account->email)) &&
            (!empty($account->external_accounts->data[0]->id)) &&
            (!empty($account->external_accounts->data[0]->account)) &&
            (!empty($account->legal_entity->address)) &&
            (true == $account->legal_entity->business_tax_id_provided) &&
            (true == $account->legal_entity->personal_id_number_provided) &&
            (true == $account->legal_entity->ssn_last_4_provided) &&            
            (true == $account->payouts_enabled)
        );
    }        
}
