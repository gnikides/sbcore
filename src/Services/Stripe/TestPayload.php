<?php namespace Core\Services\Stripe;

class TestPayload
{
    public function create()
    { 
        return [
            'account_type'              => 'custom',
            'email'                     => 'patrickkane75@gmail.com',
            'business_type'             => 'company',
            'business_name'             => 'Test Company SA',
            'business_tax_id'           => '521143198',
            'business_vat_id'           => 'FR30530646751',
            'debit_negative_balances'   => '1',
            'delay_days'                => 'minimum',
            'default_currency'          => 'eur',
            'interval'                  => 'manual',
            'first_name'                => 'Sterling',
            'last_name'                 => 'Granger',
            'dob_day'                   => '24',
            'dob_month'                 => '10',
            'dob_year'                  => '1971',
            'personal_id_number'        => '162109940120809',
            'tos_acceptance_date'       => time(),
            'tos_acceptance_ip'         => '192.168.255.37',

            'company_address1'          => '17 rue de la Tour',
            'company_city'              => 'Paris',
            'company_state'             => '',
            'company_postcode'          => '75116',
            'company_country_code'      => 'FR',            
            // 'business_address1'         => '17 rue de la Tour',
            // 'business_city'             => 'Paris',
            // 'business_state'            => '',
            // 'business_postcode'         => '75116',
            // 'business_country_code'     => 'FR',

            'individual_address1'       => '17 rue de la Tour',
            'individual_city'           => 'Paris',
            'individual_state'          => '',
            'individual_postcode'       => '75116',
            'individual_country_code'   => 'FR',
            // 'personal_address1'         => '17 rue de la Tour',
            // 'personal_city'             => 'Paris',
            // 'personal_state'            => '',
            // 'personal_postcode'         => '75116',
            // 'personal_country_code'     => 'FR',            
            
            'bank_account_holder_name'  => 'Jane Austen',
            'bank_account_holder_type'  => 'individual',
            'bank_account_number'       => 'DE89370400440532013000',
            'routing_number'            => '110000000',
            'bank_account_country'      => 'DE',
            'bank_account_currency'     => 'eur'
        ];
    }
}   