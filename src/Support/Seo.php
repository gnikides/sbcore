<?php namespace Story;

use Crop;

class Seo
{   
	private $troubleWords = array(
		'&ltpgt',
		'ltpgt',
		'&lt;p&gt;', 
		'&amp;',
		'&amp',
		'nbsp;',
		'nbsp'
	);
	private $troubleCharacters = array('(',
		')',
		'\\',
		'/',
		'--'
	);    
    private $stopwords = array('i','a','about','an','and','are','as','at','be','by','com',
    	'de','en','for','from','how','in','is','it','la','of','on','or','that',
        'the','this','to','was','what','when','where','while','who','will','with',
        'und','the','www');
              
              		 
    public function makeUrlKey($str)
    {
        // only attempt to modify scalar values (numbers, strings, boolean)
        if (!is_scalar($str)) {
            return false;
        }
        $str = (string)$str;
        
        $str = strip_tags($str);        
        $str = str_replace(array( "\n", "\r", "\t" ), ' ', $str);               
		$str = str_replace($this->troubleWords, '', $str);
        
        // Some characters that might create trouble
        $str = str_replace($this->troubleCharacters, '-', $str);

        // Leave only alphanumeric characters and replace spaces with hyphens
        $str = strtolower(preg_replace('/[\s]/', '-', preg_replace('/[^[:alnum:]\s-]+/', '', $str)));

        // Trim it so that we don't have hyphens at either end of the seo name
        return trim($str, '-');
    }
    
    public function extractCommonWords($str, $limit='20', $stopwords='')
    {
    	if (empty($stopwords)) {
    		$stopwords = $this->stopwords;
    	}
    	
    	$str = strip_tags($str);
    	$str = str_replace(array( "\n", "\r", "\t" ), ' ', $str);
		$str = str_replace($this->troubleWords, ' ', $str);	
					
        $str = trim(preg_replace('/\s\s+/i', '', $str));      // replace whitespace         
        $str = preg_replace('/[^a-zA-Z0-9 -]/', '', $str);    // keep alphanumerical chars, spaces, dashes
        $str = strtolower($str);                              // make it lowercase

		preg_match_all('/\b.*?\b/i', $str, $matchWords);
		$matchWords = $matchWords[0];
		
		foreach ($matchWords as $key => $item ) {
		  	if ($item == '' || in_array(strtolower($item), $stopwords) || strlen($item) <= 3 ) {
			  	unset($matchWords[$key]);
			}
		}
		//var_dump($matchWords);		
		$wordCounter = array();
		if (is_array($matchWords) ) {
		  foreach ($matchWords as $v) {
			  $v = strtolower($v);		  			        	  
			  if (isset($wordCounter[$v]) ) {
				  $wordCounter[$v]++;
			  } else {
				  $wordCounter[$v] = 1;
			  }
		  }
		}
		arsort($wordCounter);
		$wordCounter = array_slice($wordCounter, 0, $limit);
		//var_dump($wordCounter); exit();
		return array_unique(array_keys($wordCounter));
    }
    
    public function makeMetaKeywords($str, $limit='20')
    {
        return implode(',', $this->extractCommonWords($str, $limit));
    } 
    
    public function makeMetaDescription($str, $limit='160')
    {
        $str = strip_tags($str);
    	$str = str_replace($this->troubleWords, ' ', $str);
    	$str = str_replace(array( "\n", "\r", "\t", '  '), ' ', $str);
    	$str = str_replace($this->troubleCharacters, '', $str);
    	return Crop::cropAtSpace($str, $limit);
    }

    public function getStopwords()
    {   
        return $this->stopwords;
    }        
}