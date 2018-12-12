<?php namespace Core\Services\Stripe;

class Transaction
{
    public function charge(
        $total,
        $currency_code,
        $provider_customer_id,
        $provider_merchant_id,
        $application_fee = '',
        $charge_description = '',
        $statement_description = '',
        $metadata = []
    ) {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $charge = [
            'amount'                => $total,
            'currency'              => strtolower($currency_code),
            'customer'              => $provider_customer_id,
            'destination'           => $provider_merchant_id,
            'description'           => $charge_description,
            'statement_descriptor'  => $statement_description,
            'capture'               => true,
            'application_fee'       => $application_fee,
            'metadata'              => $metadata
            //['stripe_account' => '{CONNECTED_STRIPE_ACCOUNT_ID}')];
            //['stripe_account'         => 'acct_1BTn2jErNBNQFUc6']            
        ];           
        if ($response['failure_code']) {
            \Log::error('Charge failed for customer # ' . $provider_customer_id, [json_encode($response)]);            
        } 
        return $response;
    } 
}    