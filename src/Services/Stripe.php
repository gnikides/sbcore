<?php namespace App\Modules;

use App\Support\Cache\Manager as CacheManager;
use App\Models\BankAccount;
use App\Models\Address;

class Stripe
{
    /**
     * Stripe account id
     *
     * @var integer
     */ 
    protected $account_id;

    /**
     * Stripe account
     *
     * @var integer
     */ 
    protected $account;
    
    /**
     * Cache provider
     *
     * @var object
     */    
    protected $cache;

    protected $business_type = 'company';
    protected $business_name;   
    protected $business_address;        
    protected $personal_address;    
    protected $dob; 
    protected $first_name;  
    protected $last_name;   
    protected $bank_account;    
    
    // todo ?
//  protected $settings;    
//  protected $bank_account;    
//  protected $balance; 
//  protected $transactions;
//  protected $payouts;
        
    /**
     * Create a new CartState instance
     *
     * @internal param object $presenter
     */
    public function __construct($account_id)
    {   
        $this->account_id   = $account_id;
        
        if ($this->account_id) {
            $this->prefix   = 'stripe';             
            $this->cache    = new CacheManager('memcached', $this->prefix); 
            $this->setup();          
        }
        $this->format();                    
    }   
    
    /**
     * Setup
     *
     *  @gotcha 
     *  If no items, 0 returned from cache
     *  If nothing in cache, null returned
     *  Prevents cache from being hit every time     
     *
     * @return object
     */ 
    public function setup()
    {   
        $this->account = $this->cache->get($this->account_id);

        if (null === $this->account) {
            
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $this->account = \Stripe\Account::retrieve($this->account_id);
            
            if (empty($this->account)) {
                $this->account = 0;
            }

            $timeout = $this->cache->putTimeout($this->account_id);
            $this->cache->put($this->account_id.':'.$timeout, $this->account);                          
        }
        
        return $this;
    }

    
    /**
     * Format account values for use in app
     *
     * @param  array $items
     * @return array
     */
    public function format()
    {
        if ($this->account) {
            
            $this->business_name        = $this->account['business_name'];
            $this->business_type        = $this->account['legal_entity']['type'];
                                                
            $this->business_address = new Address([
                'address1'      => @$this->account['legal_entity']['address']['line1'], 
                'address2'      => @$this->account['legal_entity']['address']['line2'],
                'city'          => @$this->account['legal_entity']['address']['city'],  
                'postcode'      => @$this->account['legal_entity']['address']['postal_code'],
                'state'         => @$this->account['legal_entity']['address']['state'],
                'country'       => ['id' => @$this->account['legal_entity']['address']['country'] ]
            ]);
            $this->personal_address = new Address([
                'address1'      => @$this->account['legal_entity']['personal_address']['line1'],    
                'address2'      => @$this->account['legal_entity']['personal_address']['line2'],
                'city'          => @$this->account['legal_entity']['personal_address']['city'], 
                'postcode'      => @$this->account['legal_entity']['personal_address']['postal_code'],
                'state'         => @$this->account['legal_entity']['personal_address']['state'],
                'country'       => ['id' => @$this->account['legal_entity']['personal_address']['country'] ]                   
            ]);
            
            $this->dob          = $this->account['legal_entity']['dob'];
            $this->first_name   = $this->account['legal_entity']['first_name'];
            $this->last_name    = $this->account['legal_entity']['last_name'];
            
            $this->bank_account = '';
            if (isset($this->account->external_accounts->data[0]) && isset($this->account->external_accounts->data[0]->id)) {
                $this->bank_account = new BankAccount([
                    'id'                    => $this->account->external_accounts->data[0]->id,
                    'account_holder_name'   => $this->account->external_accounts->data[0]->account_holder_name,
                    'account_holder_type'   => $this->account->external_accounts->data[0]->account_holder_type,
                    'account_last4'         => $this->account->external_accounts->data[0]->last4,               
                    'routing_number'        => $this->account->external_accounts->data[0]->routing_number,
                    'country_code'          => $this->account->external_accounts->data[0]->country,
                    'currency'              => $this->account->external_accounts->data[0]->currency                     
                ]);
            } else {
                $this->bank_account = new BankAccount([]);
            }                                           
        
        } else {
            $country = ['id' => server('country_code')];
            $this->business_address     = new Address($country);
            $this->personal_address     = new Address($country);            
            $this->bank_account         = new BankAccount($country);                    
        }               
    }

    /**
     * Delete cache for this instance
     *
     * @return void
     */         
    public function expire()
    {               
        if ($this->account_id) {
            return $this->cache->expire($this->account_id); 
        }            
    }

    /**
     * Delete cache for this instance
     *
     * @return void
     */         
    public function refresh()
    {               
        if ($this->account_id) {
            return $this->cache->expire($this->account_id); 
        }            
    }
        
    public function account()
    {   
        return $this->account;
    }
                
    public function businessType()
    {   
        return $this->business_type;
    }

    public function businessName()
    {   
        return $this->business_name;
    }
            
    public function businessAddress()
    {
        return $this->business_address;     
    }

    public function personalAddress()
    {   
        return $this->personal_address; 
    }   
    
    public function dob()
    {   
        return $this->dob;  
    }
    
    public function firstName()
    {   
        return $this->first_name;   
    }
    
    public function lastName()
    {   
        return $this->last_name;    
    }
    
    public function bankAccount()
    {   
        return $this->bank_account;
    }                                   
}