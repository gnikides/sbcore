<?php namespace App\Modules;

class Currency
{  
    public function __construct($currency_code='')
    {   
        if ($currency_code) {
            $this->get($currency_code);
        }
    }
     
    public function get($code='')
    {   
        $this->items = api('currency')->get($code);
        // if ($this->items && $code) {
        //     $this->items['code'] = $code;
        // }          
        return $this;
    }

    public function getFromCountry($country_code)
    {   
        $country = api('country')->get($country_code); 
        if ($currency_code = array_get($country, 'currency_code')) {
            return $this->get($currency_code);
        }
        return false;   
    }

    public function format($price='', $is_zero_shown=false)
    {       
        if ((0 == abs($price)) && !$is_zero_shown) {
            return false;
        } 
        return trim($this->getSymbolLeft()
        . ' '       
        . $this->formatNoSymbol($price, $is_zero_shown)
        . ' '
        . $this->getSymbolRight());
    }

    public function formatNoSymbol($price, $is_zero_shown=false)
    {   
        if (empty($price) && !$is_zero_shown) {
            return false;
        } 
        if (empty($price)) {
            $price = 0;
        }           
        return $price;      
    }

    public function forceDecimal($price)
    {   
        if (empty($price)) {
            $price = 0;
        }           

        $decimal_place = $this->getDecimalPlace();
        $decimal_mark = $this->getDecimalMark();            

        return number_format(
            $price, 
            $decimal_place, 
            $decimal_mark,
            ''
        );          
    }
           
    public function getItems()
    {
        return $this->items;
    }

    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }
    
    public function getCode()
    {
        return $this->items['code'];
    }
        
    public function getTitle()
    {
        return $this->items['title'];
    }
    
    public function getSubunit()
    {
        return @$this->items['subunit'];
    }
                        
    public function getSymbol()
    {
        return $this->items['symbol'];
    }   
    
    public function getSymbolLeft()
    {   
        if ($this->getPosition() == 'L') {
            return $this->getSymbol();
        }
        return false;   
    }
    
    public function getSymbolRight()
    {   
        if ($this->getPosition() == 'R') {
            return $this->getSymbol();
        }
        return false;
    }
                    
    public function getPosition()
    {
        return @$this->items['position'];
    }
        
    public function getDecimalMark()
    {
        return @$this->items['decimal_mark'];
    }
        
    public function getDecimalPlace()
    {
        if (empty($this->items['subunit'])) {
            return 0;
        } elseif ($this->items['subunit'] == 100) { 
            return 2;
        } elseif ($this->items['subunit'] == 10) {  
            return 1;
        }           
    }
              
    public function getThousandSeparator()
    {
        return @$this->items['thousand_separator'];
    }
}
