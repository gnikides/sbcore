<?php namespace Story;

class String
{
    public static function underscoreToCamel($str)
    { 
        return str_replace(' ', '', 
        	ucwords(str_replace(array('-', '_'), ' ', $str))
        ); 
    }

    public static function camelToUnderscore($str)
    { 
        return strtolower(
        	preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", $str)
        );
    }

    // to test: works with Greek, Russian, Polish & French amongst other languages?
    public static function utf8Strtolower($str)
    { 
        $from = array( 
          "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
          "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
          "U", "V", "W", "X", "Y", "Z", "À", "Á", "Â", "Ã", 
          "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", 
          "Î", "Ï", "Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø",
          "Ù", "Ú", "Û", "Ü", "Ý", "А", "Б", "В", "Г", "Д", 
          "Е", "Ё", "Ж", "З", "И", "Й", "К", "Л", "М", "Н",
          "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", 
          "Ш", "Щ", "Ъ", "Ъ", "Ь", "Э", "Ю", "Я", "Ą", "Ć", 
          "Ę", "Ł", "Ń", "Ó", "Ś", "Ź", "Ż"
        ); 
        $to = array( 
          "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
          "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
          "u", "v", "w", "x", "y", "z", "à", "á", "â", "ã",
          "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì", "í",
          "î", "ï", "ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ø",
          "ù", "ú", "û", "ü", "ý", "а", "б", "в", "г", "д",
          "е", "ё", "ж", "з", "и", "й", "к", "л", "м", "н",
          "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", 
          "ш", "щ", "ъ", "ы", "ь", "э", "ю", "я", "ą", "ć",
          "ę", "ł", "ń", "ó", "ś", "ź", "ż" 
        ); 
        return str_replace($from, $to, $str);
    }
    
    public static function getBetween($content, $start, $end)
    {
		$str = explode($start, $content);
		if (isset($str[1])) {
			$str = explode($end, $str[1]);
			return $str[0];
		}
    	return '';
    }

    public static function makeFullname($firstname='', $lastname='')
    {
      $str = '';
      if ($firstname) {
        $str .= ucfirst($firstname);
        if ($lastname) {
          $str .= ' ' . ucfirst($lastname);
        } 
      } elseif ($lastname) {
        $str .= ucfirst($lastname);  	
      }
      return $str; 
    } 
    
    public static function findPenultimateOccurrence($haystack, $needle)
    {
    	$last = strrpos($haystack, $needle);
		if ($last === false) {
  			return false;
		}
		return strrpos($haystack, $needle, $last - strlen($haystack) - 1);
    }
    
    public static function resolveNameFromEmail($email='', $length=12)
    {        
        if (strpos($email, '@') !== false) {
              
            list($name,) 	= explode('@', $email);
            $name 			= trim(strtolower($name));
            $clean 			= '';
            
            for ($i = 0; $i < strlen($name); $i++) {
                if (false !== strpos('abcdefghijklmnopqrstuvwxyz123456789', $name[$i])) {
                    $clean .= $name[$i];
                }
            }
            if (strlen($clean) > $length) {
                $clean = substr($clean, 0, $length);
            }
            return $clean;   
        }        
        return false;
    }      
}
