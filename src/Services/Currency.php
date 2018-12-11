<?php namespace App\Modules;

use App\Exceptions\BadArgumentException;
use App\Models\ReadOnly\Currency as Model;

class Currency
{
    protected $currency_code;
    protected $currency;

    public function __construct(string $currency_code)
    {
        if (!$currency_code) {
            throw new BadArgumentException('Currency code cannot be empty');
        }   
        $this->currency_code = $currency_code;
        $this->currency = Model::where('code', $currency_code)->first();
    }
        
    /**
     * Transform price from cents
     */ 
    public function fromCents($price = null, bool $is_zero_shown = true, bool $is_decimal = true)
    {
        if (($price === null) || ($price == '')) {
            return '';
        }   
        if ((0 == abs($price)) && !$is_zero_shown) {
            return false;
        }         
        if ($price && $this->getSubunit() > 0) {
            $price = $price / $this->getSubunit();
        }        
        if ($is_decimal) {
            return number_format(
                $price, 
                $this->getDecimalPlace(), 
                $this->getDecimalMark(), 
                $this->getThousandSeparator()
            );
        } else {
            return number_format(
                $price, 
                0, 
                '', 
                $this->getThousandSeparator()
            );
        }     
    }

    /**
     * Arrive at cents while removing decimals, commas, spaces
     */
    public function toCents($price = null, int $subunit = null)
    { 
        if (($price === null) || ($price == '')) {
            return '';
        }    
        if (empty($price)) {
            return 0;
        }   
        if (null === $subunit) {
            $subunit = $this->getSubunit();
        }
                
        // remove everything except digits, commas, decimals
        $price = preg_replace("/[^0-9,.]/", '', $price);
        
        // change commas to decimals
        $price = str_replace(',', '.', $price);

        // remove all decimals except last
        $price = preg_replace('/\.(?=.*\.)/', '', $price);
        
        $decimal_pos = strlen(strrchr($price, "."));
        
        //  remove any decimals
        $price = str_replace('.', '', $price);
                
        //  instead of showing error, we try to guess what customer wants ...
        if ($decimal_pos > 3) { 
            $price = $price / pow(10, $decimal_pos-3);       
            
        } elseif ($decimal_pos == 2) {
            if (!empty($subunit)) {           
                $price = $price * $subunit/10;
            }   
        
        } elseif ($decimal_pos < 2)  {
            // no decimal position or decimal at beginning
            if (!empty($subunit)) {           
                $price = $price * $subunit;
            }
        }
        // ($decimal_pos == 3) would be most expected position, no change in value, just remove decimal         
        return (int)$price;
    }

    /**
     * Format price for display, with symbols
     */
    public function format($price = '', bool $is_zero_shown = true, bool $is_decimal = true)
    {     
        return trim($this->getSymbolLeft()
        . ' '       
        . $this->fromCents($price, $is_zero_shown, $is_decimal)
        . ' '
        . $this->getSymbolRight());
    }
        
    public function getCurrency()
    {
        return $this->currency->toArray();
    }
    
    public function getCode()
    {
        return $this->currency_code;
    }
        
    public function getTitle()
    {
        return $this->currency->title;
    }
    
    public function getSubunit()
    {
        return $this->currency->subunit;
    }
                        
    public function getSymbol()
    {
        return $this->currency->symbol;
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
        return $this->currency->position;
    }
        
    public function getDecimalMark()
    {
        return $this->currency->decimal_mark;
    }
    
    public function getDecimalPlace()
    {
        return $this->currency->decimal_place;
    }   

    public function getThousandSeparator()
    {
        return $this->currency->thousand_separator;
    } 
}   