<?php namespace App\Support\Indexer;

use App\Story\ProductReference\Model\Product;
use App\Services\SafeUrl;
use Snipe\BanBuilder\CensorWords;

class Optimizer
{     
    protected $status;
    protected $errors;
    protected $product;
        
    /**
     * Index document
     *
     * @param $product
     * @return void
     * @internal param array $input
     */
    public function optimize($product)
    { 
        $this->product = new Product($product);
                        
        $safe_url = new SafeUrl();
        $params['safe_url'] = $safe_url->make($this->product->getName());
        
        //  stop if bad wors in product
//      if (!$this->filterTexts()) {
//          $this->errors[] = 'Indexing failed, bad words #' . $this->product->getId();;
//      }
        //  suppress review if bad words in review but go ahead with indexing
        $this->filterReviews();
                
        /*
        $meta_generator = new zzzzzzzz();
        $params['meta_description']     = make($product['name']);
        $params['meta_title']           = make($product['name']);
        $params['meta_keys']            = make($product['name']);
        $params['facebook']             = make();
        $params['twitter']              = make();
        $params['google_analytics']     = make();
        $params['amazon']               = make();
        */
                
        if ($this->errors) {
            return false;
        }
        
        $params['optimized_at']     = now();     
        $update = app(ProductVersion::class)->update(
            $this->product->getId(), 
            $params,
            false
        ); 
        if ($update) {
            $this->status = true;
        }
    }

    public function filterTexts()
    { 
        $site               = app(Site::class)->find($this->product->getSiteId());
        $core_editorial     = $this->product->getCoreEditorial();
        $format_editorial   = $this->product->getFormatEditorial(); 
            
        $texts = [];
        
        $texts[] = $core_editorial['name'];
        
        $attributes = array_get($core_editorial, 'attributes');
        if ($attributes) {
            foreach ($attributes as $name => $attribute) {
                $texts[] = $name . ' ' . $attribute['value']; 
            }
        }
        $descriptions = array_get($core_editorial, 'descriptions');
        if ($descriptions) {
            foreach ($descriptions as $description) {
                $texts[] = $description['value']; 
            }
        }
        $features = array_get($core_editorial, 'features');
        if ($features) {
            foreach ($features as $feature) {
                $texts[] = $feature; 
            }
        }   
        $texts[] = @$format_editorial['name'];
        $format_attributes = array_get(@$format_editorial, 'attributes');
        if ($format_attributes) {
            foreach ($format_attributes as $name => $attribute) {
                $texts[] = $name . ' ' . $attribute['value']; 
            }
        }
        $format_descriptions = array_get($format_editorial, 'descriptions');
        if ($format_descriptions) {
            foreach ($format_descriptions as $description) {
                $texts[] = $description['value']; 
            }
        }

        $censor = new CensorWords;
        //$censor->setDictionary(['en-us','en-uk','es','fr']);
        $censor->setDictionary(['en-us','en-uk']);
                
        foreach ($texts as $text) {
            $filtered = $censor->censorString($text);

            if (@$filtered['matched'] && count($filtered['matched']) > 0) {       
                $error = 'Product ID: ' . $this->product->getId()
                . "\n\n"
                . json_encode($filtered);
                \Log::error('Elastic indexer profanity', [$error]);
                \Mail::raw($error, function($message) {
                    $message->to(trim(config('mail.admin')));
                    $message->subject('Indexer stopped on profanity');
                });
                return false;
            }    
        }
        return true;  
    }
    
    public function filterReviews()
    {
        $reviews = app(Review::class)->allByProduct($this->product->getCoreId());
        
        $censor = new CensorWords;
        $censor->setDictionary(['en-us','en-uk','es', 'fr']);
                
        foreach ($reviews as $review) {
            $filtered = $censor->censorString($review['review']);
            if (@$filtered['matched'] && count($filtered['matched']) > 0) {   
                \Log::error('Bad words in review', [$filtered]); 
            } else {  
                app(Review::class)->update($review['id'], ['is_approved' => now()]);
            }
        }
        return true;
    }
        
    /**
     * Getter finds if optimizing was successful
     *
     * @return boolean
     */ 
    public function success()
    {
        return $this->status;
    }
    
    /**
     * Getter finds if optimizing was successful
     *
     * @return boolean
     */ 
    public function errors()
    {
        return $this->errors;
    }
}