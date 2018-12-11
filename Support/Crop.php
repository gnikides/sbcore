<?php namespace Story;

class Crop
{    
    /**
    * @var int
    *
    * How far ahead in the string should we look for a period, so we can crop the text
    */    
    const EXTRA_SPACE = 0;

    /**
    * @var int
    *
    * How short should the text be before we scrap cropping at spaces and periods?
    */    
    const MIN_LENGTH = 10;
            
    /**
     * Do initial cropping
     *
     * Make length a bit longer so we have space to look for sentence end
       *
       * @param   string  $text
       * @param   int     $length     Optional
       * @return    string
      */
    private static function __doInitialCrop($text, $length=400)
    {
        return substr($text, 0, ($length + self::EXTRA_SPACE));
    }

    /**
     * Ensure string is not too short - if it is go back to original string
      *
      * @param   string  $text
      * @param   string  $originalText
      * @param   int     $length     Optional
      * @return    string
     */
    private static function __ensureMinLength($text, $originalText, $length=400)
    {
        $text = trim($text);
        if (strlen($text) < self::MIN_LENGTH) {
            return substr($originalText, 0, $length);
        }
        return $text;
    }
    
    /**
     * Crop text at end of sentence
     *
     * @param   string  $originalText
     * @param   int     $length     Optional
     * @param   string  $message    Optional
     * @return    string
    */ 
    public static function cropAtSentence($originalText, $length=400, $message='')
    {        
        if (strlen($originalText) < $length) {
            return $originalText; 
        
        } else {
        
			$text = self::__doInitialCrop($originalText, $length);
		
			$pos = strrpos($text, ". ");
					
			if ($pos !== false) {        

				//    ensure "." is included
				$text = substr($text, 0, ($pos + 1));

				$text = self::__ensureMinLength($text, $originalText, $length);

				if (!empty($message)) {
					$text = $text . ' ' . $message;
				}
				return trim($text);
			}
		}	
        return false;
    }
    
    /**
     * Crop text at space
      *
      * @param   string  $original_text
      * @param   int     $length     Optional
      * @param   string  $message    Optional
      * @return    string
     */
    public static function cropAtSpace($originalText, $length=400, $message='')
    {
        if (strlen($originalText) < $length) {
            return $originalText; 
        
        } else {
        
			$text = self::__doInitialCrop($originalText, $length);

			$pos = strrpos($text, " ");
		
			if ($pos !== false) {
				$text = substr($text, 0, $pos);
			
				$text = self::__ensureMinLength($text, $originalText, $length);
			
				if (!empty($message)) {
					$text = $text . ' ' . $message;
				}
				return trim($text);
			}
			return $text;
		}
		return false;	
    }
    
    /**
     * Crop text looking for ends of sentences or, if none, spaces
      *
      * @param   string  $text
      * @param   int     $length     Optional
      * @param   string  $message    Optional
      * @return    string
     */
    public static function crop($text, $length=400, $message='')
    {    
        if (strlen($text) > $length) {
            
            //    first, look for end of sentence
            if ($newText = self::cropAtSentence($text, $length, $message)) {
                return $newText;
            } else {   
                //    couldn't find end of sentence, so look for word break
                return (self::cropAtSpace($text, $length, $message));
            }
        }
        return trim($text);        
    }
    
    public static function splitAtSpace($text, $length=400)
    {
       	$start = self::cropAtSpace($text, $length, '');       	
       	$end = substr($text, strlen($start));
       	return array($start, $end);
    }          
}