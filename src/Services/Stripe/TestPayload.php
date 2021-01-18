<?php namespace Core\Services\Stripe;

class TestPayload
{
    public function create()
    { 
        return [
            'account_type'              => 'custom',
            'country'                   => 'FR',
            'email'                     => 'patrickkane75@gmail.com',

            //   company
            'business_type'             => 'company',
            'company_tax_id'            => '521143198',
            'company_vat_id'            => 'FR30530646751',
            'registration_number'       => '',            
            'company_structure'         => 'private_corporation',
            'company_name'              => 'Test Company SA',
            'company_address1'          => '17 rue de la Tour',
            'company_address2'          => '',
            'company_city'              => 'Paris',
            'company_state'             => '',
            'company_postcode'          => '75116',
            'company_country_code'      => 'FR',            
            'company_phone'             => '621049270',  

            //   capabilities
            'request_card_payments'     => true,
            'request_transfers'         => true,

            //  business profile
            'business_profile_url'      => 'www.storybloks.com',
            'business_profile_mcc'      => '7372',  // computer programming
            'debit_negative_balances'   => true,
            // 'delay_days'                => 'minimum',
            'default_currency'          => 'eur',
            // 'interval'                  => 'manual',

            'personal_id_number'        => '162109940120809',
            'tos_acceptance_date'       => time(),
            'tos_acceptance_ip'         => '192.168.255.37',

            //   individual
            'individual_address1'       => '17 rue de la Tour',
            'individual_address2'       => '',
            'individual_city'           => 'Paris',
            'individual_state'          => '',
            'individual_postcode'       => '75116',
            'individual_country_code'   => 'FR',     
            
            // representative
            'representative_first_name'     => 'Sterling',
            'representative_last_name'      => 'Granger',
            'representative_title'          => 'President',            
            'representative_dob_day'        => '24',
            'representative_dob_month'      => '10',
            'representative_dob_year'       => '1971',
            'representative_email'          => 'patrickkane75@gmail.com',
            'representative_phone'          => '621049270',
            'representative_relationship'   => 'representative',

            // director ...

            // owner ...

            //   bank account
            'bank_account_holder_name'      => 'Jane Austen',
            'bank_account_holder_type'      => 'individual',
            'bank_account_number'           => 'DE89370400440532013000',
            'routing_number'                => '110000000',
            'bank_account_country'          => 'DE',
            'bank_account_currency'         => 'eur'
        ];
    }
}   