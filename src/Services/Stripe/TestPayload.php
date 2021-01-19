<?php namespace Core\Services\Stripe;

class TestPayload
{
    public function create()
    { 
        $person = [
            'title'             => 'President',
            'first_name'        => 'Sterling',
            'last_name'         => 'Granger',
            'address1'          => '17 rue de la Tour',
            'address2'          => '',
            'city'              => 'Paris',
            'state'             => '',
            'postcode'          => '75116',
            'country_code'      => 'FR',            
            'phone'             => '000 000 0000',         
            'dob_day'           => '24',
            'dob_month'         => '10',
            'dob_year'          => '1971',
            'email'             => 'patrickkane75@gmail.com'           
        ];    
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
            'company_directors_provided'=> true,    
            'company_owners_provided'   => true,
            'company_executives_provided'=> true,

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
            'representative_title'          => $person['title'],         
            'representative_address1'       => $person['address1'], 
            'representative_address2'       => $person['address2'], 
            'representative_city'           => $person['city'], 
            'representative_state'          => $person['state'], 
            'representative_postcode'       => $person['postcode'], 
            'representative_country_code'   => $person['country_code'],    
            'representative_dob_day'        => $person['dob_day'],
            'representative_dob_month'      => $person['dob_month'],
            'representative_dob_year'       => $person['dob_year'],
            'representative_email'          => $person['email'],
            'representative_phone'          => $person['phone'],
            'representative_is_representative'=> true,
            'representative_is_owner'       => true,
            'representative_is_director'    => true,
            'representative_is_executive'   => true,

            // director ...
            'director_first_name'       => $person['first_name'],
            'director_last_name'        => $person['last_name'],
            'director_title'            => $person['title'],         
            'director_address1'         => $person['address1'], 
            'director_address2'         => $person['address2'], 
            'director_city'             => $person['city'], 
            'director_state'            => $person['state'], 
            'director_postcode'         => $person['postcode'], 
            'director_country_code'     => $person['country_code'],    
            'director_dob_day'          => $person['dob_day'],
            'director_dob_month'        => $person['dob_month'],
            'director_dob_year'         => $person['dob_year'],
            'director_email'            => $person['email'],
            //'director_phone'            => $person['phone'],
            // 'director_relationship'     => true, 

            // owner ...
            'owner_first_name'          => $person['first_name'],
            'owner_last_name'           => $person['last_name'],
            'owner_title'               => $person['title'],         
            'owner_address1'            => $person['address1'], 
            'owner_address2'            => $person['address2'], 
            'owner_city'                => $person['city'], 
            'owner_state'               => $person['state'], 
            'owner_postcode'            => $person['postcode'], 
            'owner_country_code'        => $person['country_code'],    
            'owner_dob_day'             => $person['dob_day'],
            'owner_dob_month'           => $person['dob_month'],
            'owner_dob_year'            => $person['dob_year'],
            'owner_email'               => $person['email'],
            //'owner_phone'               => $person['phone'],
            //'relationship_owner'        => true,

            //   bank account
            'bank_account_holder_name'  => 'Jane Austen',
            'bank_account_holder_type'  => 'individual',
            'bank_account_number'       => 'DE89370400440532013000',
            'routing_number'            => '110000000',
            'bank_account_country'      => 'DE',
            'bank_account_currency'     => 'eur'
        ];
    }
}   