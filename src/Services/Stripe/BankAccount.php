<?php namespace Core\Services\Stripe;

class BankAccount
{  
    public function create($platform_key, array $payload)
    {  
        $stripe = new \Stripe\StripeClient([
            "api_key" => config('services.stripe.secret'),
            "stripe_version" => config('services.stripe.version')            
        ]);

        $account = $stripe->accounts->retrieve($platform_key);  
        if ($account->id) {
            $params = [
                'object'                => 'bank_account',
                'account_holder_name'   => $payload['bank_account_holder_name'],
                'account_holder_type'   => $payload['bank_account_holder_type'],                    
                'account_number'        => $payload['bank_account_number'],
                //'routing_number'        => ($payload['routing-number']) ? $payload['routing-number'] : '',
                'country'               => $payload['bank_account_country'],                    
                'currency'              => $payload['bank_account_currency']                                    
            ];
            $response = $account->external_accounts->create([
                'external_account' => $params
            ]);          
            if ($response['id'] && empty($response['failure_code'])) {
                return $response['id']; 
            }
        }
        \Log::error('merchant bank account failed', [$account, $params, @$response]);
        return false;  
    }
}
