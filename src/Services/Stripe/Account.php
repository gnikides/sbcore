<?php namespace Core\Services\Stripe;

use Core\Services\Stripe\Helper;

class Account
{ 
    public function create(array $payload)
    {  
        $payload = $this->formatPayload($payload);              
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
            'company_directors_provided'    => 'company.directors_provided',  
            'company_owners_provided'       => 'company.owners_provided',  
            'company_executives_provided'   => 'company.executives_provided',  

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
