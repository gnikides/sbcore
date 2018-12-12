<?php namespace Core\Services\Text;

use Snipe\BanBuilder\CensorWords;

class BadWords
{
    protected $censor;
    protected $errors;
    protected $dictionaries = ['en-us','en-uk','es', 'fr'];

    public function filter($text, $stop_on_errors = true)
    {
        $censor = new CensorWords;
        $censor->setDictionary($this->dictionaries);
        $filtered = $censor->censorString($text);
        if (@$filtered['matched'] && count($filtered['matched']) > 0) { 
            $this->errors = $filtered;  
            \Log::error('Bad words in text', [$filtered]);
            if (true == $stop_on_errors) {
                return false;
            } 
        }
        return true;
    }
        
    public function setDictionaries($dictionaries)
    {
        return $this->dictionaries = $dictionaries;
    }
    
    public function errors()
    {
        return $this->errors;
    }
}