<?php namespace App\Services;

class SafeUrl
{
    private $options = [
        'maxlength'         => 50,
        'separator'         => '-',
        'decode_charset'    => 'UTF-8',
        'dummy_text'        => 'our-great-new-product'                  
    ];

    /**
     * Table for decoding special characters
     */
    private $translation_table = [
        'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj','Ð'=>'Dj','đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y',
        'Ŕ'=>'R', 'ŕ'=>'r', 'ē'=>'e',
        /**
         * Special characters:
         */
        "'"    => '',       // Single quote
        '&'    => ' and ',  // Ampersand
        "\r\n" => ' ',      // Newline
        "\n"   => ' '       // Newline
   ];

    /**
     * Trouble words
     *
     * @var array
     */
    private $troubleWords = [
        '&ltpgt',
        'ltpgt',
        '&lt;p&gt;', 
        '&amp;',
        '&amp',
        'nbsp;',
        'nbsp'
    ];

    /**
     * Trouble characters
     *
     * @var array
     */ 
    private $stopwords = [
            
        // english
        'a',
        'an',
        'that',
        'the',

        // french
        'le',
        'la',
        'une',
        'un'
        
        // german
    ];

    public function __construct(array $options = [])
    {
        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }
    }

    public function make(string $text)
    {
//         if (!is_scalar($text)) {
//            throw new \Exception('Text is not scalar');
//         }
        $text = (string)$text;
        
        //  replace trouble characters & words
        $text = str_replace(['\n', '\r', '\t' ], ' ', $text);     
        $text = str_replace($this->troubleWords, '', $text);
                    
        // decode UTF-8 chars
        $text = html_entity_decode($text, ENT_QUOTES, $this->options['decode_charset']);  
        $text = strtr($text, $this->translation_table);        
        
        // strip HTML & lowercase
        $text = strip_tags(trim(strtolower($text)));

        // filter the input for non alphanumeric
        $text = preg_replace("/[^&a-z0-9-_\s']/i", '', $text);
        
        //   add separator in whitespaces                
        $text = str_replace(' ', $this->options['separator'], $text);
        $text = preg_replace("/{$this->options['separator']}{2,}/", $this->options['separator'], $text);

        //   crop at maxlength, maintaining integrity of last word
        if (strlen($text) > $this->options['maxlength']) {
            $text = substr($text, 0, $this->options['maxlength']);
            $pos = strrpos($text, $this->options['separator']);  
            if ($pos !== false) {
                $text = substr($text, 0, $pos);
            }   
            $text = rtrim($text);
        }
                
        // remove any trailing separators
        return trim($text, $this->options['separator']);
    }
}    

