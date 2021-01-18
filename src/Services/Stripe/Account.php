<?php namespace Core\Services\Stripe;

class Account
{ 
    public function create(array $payload)
    {  
        $payload = $this->formatPayload($payload);        
        // \Stripe\Stripe::setApiKey(config('services.stripe.secret'));        
        // $response = \Stripe\Account::create($payload);        
        $stripe = new \Stripe\StripeClient([
            "api_key" => config('services.stripe.secret'),
            "stripe_version" => config('services.stripe.version')            
        ]);
        $response = $stripe->accounts->create($payload);    
        if ($response->id && empty($response->failure_code)) {
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
        $stripe = new \Stripe\StripeClient([
            "api_key" => config('services.stripe.secret'),
            "stripe_version" => config('services.stripe.version')            
        ]);
        $account = $stripe->accounts->retrieve($account_id);        
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

    public function getFormat()
    {
        return [
            'account_type'                  => 'type',
            'company_country_code'          => 'country',
            'email'                         => 'email',
            'business_type'                 => 'business_type',
            'default_currency'              => 'default_currency',

            // company
            'company_tax_id'                => 'company.tax_id',
            'company_vat_id'                => 'company.vat_id',
            'registration_number'           => 'company.registration_number',           
            'company_structure'             => 'company.structure',
            'company_name'                  => 'company.name',            
            'company_phone'                 => 'company.phone', 

            'company_address1'              => 'company.address.line1',
            'company_address2'              => 'company.address.line2',
            'company_city'                  => 'company.address.city',
            'company_state'                 => 'company.address.state',
            'company_postcode'              => 'company.address.postal_code',
            'company_country_code'          => 'company.address.country',

            //   capabilities
            'request_card_payments'         => 'capabilities.card_payments.requested',
            'request_transfers'             => 'capabilities.transfers.requested',
            'request_card_issuing'          => 'capabilities.card_issuing.requested',

            //  business profile
            'business_profile_url'          => 'business_profile.url',
            'business_profile_mcc'          => 'business_profile.mcc',
            'business_profile_name'         => 'business_profile.name',

            // settings
            'debit_negative_balances'       => 'settings.payouts.debit_negative_balances',
            'payout_schedule'               => 'settings.payouts.schedule',
            'payout_statement_descriptor'   => 'settings.payouts.statement_descriptor',
            'payments_statement_descriptor' => 'settings.payments.statement_descriptor',
            'decline_charge_on'             => 'settings.card_payments.decline_on',
            'card_payments_statement_descriptor_prefix' => 'settings.card_payments.statement_descriptor_prefix',
    
            // 'delay_days'                => 'minimum',
            // 'interval'                  => 'manual',

            // representative
            // 'representative_first_name' => 'representative.first_name',
            // 'representative_last_name'  => 'representative.last_name',
            // 'representative_title'      => 'representative.title',          
            // 'representative_dob_day'    => 'representative.dob.day',
            // 'representative_dob_month'  => 'representative.dob.month',
            // 'representative_dob_year'   => 'representative.dob.year',
            // 'representative_email'      => 'representative.email',
            // 'representative_phone'      => 'representative.phone',

            //'personal_id_number'        => '162109940120809',

            // individual
            // 'individual_first_name'         => 'individual.first_name',
            // 'individual_last_name'          => 'individual.last_name',
            // 'individual_title'              => 'individual.title',          
            // 'individual_dob_day'            => 'individual.dob.day',
            // 'individual_dob_month'          => 'individual.dob.month',
            // 'individual_dob_year'           => 'individual.dob.year',
            // 'individual_email'              => 'individual.email',
            // 'individual_phone'              => 'individual.phone',
            
            //   tos acceptance
            'tos_acceptance_date'           => 'tos_acceptance.date',
            'tos_acceptance_ip'             => 'tos_acceptance.ip',
            'metadata'                      => 'metadata'

        ];    
    }

    public function formatPayload(array $input)
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

    public function formatPayloadXXXXXXXXXXXXXX(array $input)
    {     
        /* company address */
        $company_address = [
            'line1'             => $input['company_address1'],
            'line2'             => @$input['company_address2'],
            'city'              => $input['company_city'],
            'postal_code'       => $input['company_postcode']                    
        ];
        if ($input['company_state']) {
            $company_address['state'] = $input['company_state'];
        }

        /* legal entity */                                        
        // $legal_entity = [
        //     'type'                  => $input['business_type'],
        //     'business_name'         => $input['business_name'],
        //     'business_tax_id'       => $input['business_tax_id'],                                            
        //     'first_name'            => $input['first_name'],
        //     'last_name'             => $input['last_name'],
        //     'personal_id_number'    => $input['personal_id_number'],
        //     'address'               => $business_address,
        //     'dob'                   => [
        //         'day'               => $input['dob_day'],
        //         'month'             => $input['dob_month'],
        //         'year'              => $input['dob_year'],            
        //     ],
        //     'personal_address'      => $personal_address    
        // ];
        $company = [
            'name'                  => $input['company_name'],
            'structure'             => $input['company_structure'],
            'tax_id'                => $input['company_tax_id'],
            'vat_id'                => $input['company_vat_id'],
            'registration_number'   => $input['registration_number'],

            // 'first_name'            => $input['first_name'],
            // 'last_name'             => $input['last_name'],
            // 'personal_id_number'    => $input['personal_id_number'],
            'address'               => $company_address,
            // 'dob'                   => [
            //     'day'               => $input['dob_day'],
            //     'month'             => $input['dob_month'],
            //     'year'              => $input['dob_year'],            
            // ],
            // 'personal_address'      => $personal_address    
        ];
        if (array_key_exists('company_phone', $input) && $input['company_phone']) {
            $company['phone'] = $input['company_phone'];    
        }

        $capabilities = [];
        if (array_key_exists('request_card_payments', $input)) {
            $capabilities['card_payments']['requested'] = $input['request_card_payments'];    
        }
        if (array_key_exists('request_transfers', $input)) {
            $capabilities['transfers']['requested'] = $input['request_transfers'];    
        }

        $business_profile = [];
        if (array_key_exists('business_profile_url', $input) && $input['business_profile_url']) {
            $business_profile['url'] = $input['business_profile_url'];    
        }        
        if (array_key_exists('business_profile_mcc', $input) && $input['business_profile_mcc']) {
            $business_profile['mcc'] = $input['business_profile_mcc'];    
        } 

        $representative = [];

        $array = [
            'representative_first_name' => 'dob.first_name'
        ];
        foreach ($input_values as $value) {
            $representative['first_name'] = $input[$value];    
        }    
        if (array_key_exists('representative_first_name', $input) && $input['representative_first_name']) {
            $representative['first_name'] = $input['representative_first_name'];    
        }
        if (array_key_exists('representative_last_name', $input) && $input['representative_last_name']) {
            $representative['last_name'] = $input['representative_last_name'];    
        }
        if (array_key_exists('representative_dob_day', $input) && $input['representative_dob_day']) {
            $representative['dob']['day'] = $input['representative_dob_day'];    
        }
        if (array_key_exists('representative_dob_month', $input) && $input['representative_dob_month']) {
            $representative['dob']['month'] = $input['representative_dob_month'];    
        }
        if (array_key_exists('representative_dob_year', $input) && $input['representative_dob_year']) {
            $representative['dob']['year'] = $input['representative_dob_year'];    
        }        
        if (array_key_exists('representative_address_line1', $input) && $input['representative_address_line1']) {
            $representative['address']['line1'] = $input['representative_address_line1'];    
        }  
        if (array_key_exists('representative_address_line2', $input) && $input['representative_address_line2']) {
            $representative['address']['line2'] = $input['representative_address_line2'];    
        } 

        /* individual address */            
        $individual_address = [
            'line1'             => $input['individual_address1'],
            'line2'             => @$input['individual_address2'],
            'city'              => $input['individual_city'],
            'postal_code'       => $input['individual_postcode']                    
        ]; 
        if ($input['individual_state']) {
            $individual_address['state']     = $input['individual_state'];
        }        
        $individual = [
            'address'               => $individual_address,
        ];                        
        $tos_acceptance             = [
            'date'                  => $input['tos_acceptance_date'],
            'ip'                    => $input['tos_acceptance_ip']
        ];        
        $payload = [
            'country'               => $input['company_country_code'],
            'type'                  => $input['account_type'],
            'capabilities'          => $capabilities,    

            'business_type'         => $input['business_type'],            
            'email'                 => $input['email'],
            'company'               => $company,
            'business_profile'      => $business_profile,
            //'individual'            => $individual,
            'default_currency'      => $input['default_currency'],
            'tos_acceptance'        => $tos_acceptance,
            'metadata'              => []                                                                         
        ];
        //sb($payload);
        // @gotcha json transforms true to 1 but stripe expects only true, false
        // if ('1' == $input["debit_negative_balances"]) {
        //     $payload["debit_negative_balances"] = true;
        // } elseif ('0' == $input["debit_negative_balances"]) {  
        //     $payload["debit_negative_balances"] = false;
        // }
        // sb($payload);
        // exit();
        return $payload;  
    }
    
    public function verify($account_id)
    {
        $stripe = new \Stripe\StripeClient([
            "api_key" => config('services.stripe.secret'),
            "stripe_version" => config('services.stripe.version')            
        ]);
        $account = $stripe->accounts->retrieve($account_id); 
        // sb($account->charges_enabled);
        // sb($account->payouts_enabled);
        // sb($account->details_submitted);
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
            ('account' == $account->object)
            // (true == $account->charges_enabled) &&
            // (true == $account->details_submitted) &&
            // (!empty($account->email)) &&
            // (!empty($account->external_accounts->data[0]->id)) &&
            // (!empty($account->external_accounts->data[0]->account)) &&
            // (!empty($account->legal_entity->address)) &&
            // (true == $account->legal_entity->business_tax_id_provided) &&
            // (true == $account->legal_entity->personal_id_number_provided) &&
            // (true == $account->legal_entity->ssn_last_4_provided)
            // (true == $account->payouts_enabled)
        );
    }        
}
