<?php namespace App\Services\Stripe;

class BankAccount
{  
    public function create($stripe_account_id, array $payload)
    {  
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));         
        $account = \Stripe\Account::retrieve($stripe_account_id);  
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
