<?php namespace App\Services\Text;

class Crop
{  
    public static function truncate(string $text, int $length = 400, string $break_at = ' ', string $message = null)
    {
        $min_length = 10;
        if (strlen($text) < $length) {
            return $text;
        }
        //   add a bit of text so we can find "space"
        $truncated = substr($text, 0, ($length + 3));
        $pos = strrpos($truncated, $break_at);

        if ($pos !== false) {
            $truncated = trim(substr($truncated, 0, $pos));
            //  ensure not too short
            if (strlen($truncated) < $min_length) {
                $truncated = substr($text, 0, $length);
            }
        }
        if ($message) {
            $truncated .= ' ' . $message;
        }
        return $truncated;
    }
}    