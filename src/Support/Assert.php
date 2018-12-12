<?php namespace Core\Support;

use Core\Exceptions\BadArgumentException;

class Assert
{
    public static function string($value)
    {
        if (!is_string($value)) {         
            static::badType('string', $value);
        }
    }

    public static function stringNotEmpty($value)
    {
        static::string($value);
        static::notEmpty($value);
    }
    
    /*
     *  Check if number is any of 109, 1.09, '1.09', '1,09'
     */
    public static function numberish($value)
    {
        if (!is_int($value) 
            && !is_float($value) 
            && !is_numeric($value) 
            && !is_numeric(str_replace(',','.',$value))) {
            static::badType('numberish', $value);
        }
    }
            
    public static function integer($value)
    {
        if (!is_int($value)) {
           static::badType('integer', $value);
        }
    }

    public static function float($value)
    {
        if (!is_float($value)) {
            static::badType('float', $value);
        }
    }

    public static function boolean($value)
    {
        if (!is_bool($value)) {
            static::badType('boolean', $value);
        }
    }

    public static function scalar($value)
    {
        if (!is_scalar($value)) {
            static::badType('scalar', $value);
        }
    }

    public static function object($value)
    {
        if (!is_object($value)) {
            static::badType('object', $value);
        }
    }

    public static function resource($value, $type = null)
    {
        if (!is_resource($value)) {
            static::badType('resource', $value);
        }

        if ($type && $type !== get_resource_type($value)) {
            static::badArgument(sprintf(
                'Expected a resource of type %2$s. Got: %s',
                static::typeToString($value),
                $type
            ));
        }
    }

    public static function isCallable($value)
    {
        if (!is_callable($value)) {
           static::badType('callable', $value);
        }
    }

    public static function isArray($value)
    {
        if (!is_array($value)) {
            static::badType('array', $value);
        }
    }

    public static function isInstanceOf($value, $class)
    {
        if (!($value instanceof $class)) {
            static::badArgument(sprintf(
                'Expected an instance of %2$s. Got: %s',
                static::typeToString($value),
                $class
            ));
        }
    }

    public static function notInstanceOf($value, $class)
    {
        if ($value instanceof $class) {
            static::badArgument(sprintf(
                'Expected an instance other than %2$s. Got: %s',
                static::typeToString($value),
                $class
            ));
        }
    }

    public static function isEmpty($value)
    {
        if (!empty($value)) {
            static::badType('empty', $value);
        }
    }

    public static function notEmpty($value)
    {
        if (empty($value)) {
            static::badType('non-empty', $value);
        }
    }

    public static function null($value)
    {
        if (null !== $value) {
           static::badType('null', $value);
        }
    }

    public static function notNull($value)
    {
        if (null === $value) {
            static::badType('not_null', $value);
        }
    }

    public static function true($value)
    {
        if (true !== $value) {
            static::badType('true');
        }
    }

    public static function false($value)
    {
        if (false !== $value) {
            static::badType('false');
        }
    }

    public static function eq($value, $value2)
    {
        if ($value2 != $value) {
            static::badArgument(sprintf(
                'Expected a value equal to %2$s. Got: %s',
                static::valueToString($value),
                static::valueToString($value2)
            ));
        }
    }

    public static function notEq($value, $value2)
    {
        if ($value2 == $value) {
            static::badArgument(sprintf(
                'Expected a different value than %s.',
                static::valueToString($value2)
            ));
        }
    }

    public static function same($value, $value2)
    {
        if ($value2 !== $value) {
            static::badArgument(sprintf(
                'Expected a value identical to %2$s. Got: %s',
                static::valueToString($value),
                static::valueToString($value2)
            ));
        }
    }

    public static function notSame($value, $value2)
    {
        if ($value2 === $value) {
            static::badArgument(sprintf(
                'Expected a value not identical to %s.',
                static::valueToString($value2)
            ));
        }
    }

    public static function greaterThan($value, $limit)
    {
        if ($value <= $limit) {
            static::badArgument(sprintf(
                'Expected a value greater than %2$s. Got: %s',
                static::valueToString($value),
                static::valueToString($limit)
            ));
        }
    }

    public static function greaterThanEq($value, $limit)
    {
        if ($value < $limit) {
            static::badArgument(sprintf(
               'Expected a value greater than or equal to %2$s. Got: %s',
                static::valueToString($value),
                static::valueToString($limit)
            ));
        }
    }

    public static function lessThan($value, $limit)
    {
        if ($value >= $limit) {
            static::badArgument(sprintf(
                'Expected a value less than %2$s. Got: %s',
                static::valueToString($value),
                static::valueToString($limit)
            ));
        }
    }

    public static function lessThanEq($value, $limit)
    {
        if ($value > $limit) {
            static::badArgument(sprintf(
                'Expected a value less than or equal to %2$s. Got: %s',
                static::valueToString($value),
                static::valueToString($limit)
            ));
        }
    }

    public static function range($value, $min, $max)
    {
        if ($value < $min || $value > $max) {
            static::badArgument(sprintf(
                'Expected a value between %2$s and %3$s. Got: %s',
                static::valueToString($value),
                static::valueToString($min),
                static::valueToString($max)
            ));
        }
    }

    public static function oneOf($value, array $values)
    {
        if (!in_array($value, $values, true)) {
            static::badArgument(sprintf(
                'Expected one of: %2$s. Got: %s',
                static::valueToString($value),
                implode(', ', array_map(array('static', 'valueToString'), $values))
            ));
        }
    }

    public static function contains($value, $subString)
    {
        if (false === strpos($value, $subString)) {
            static::badArgument(sprintf(
                'Expected a value to contain %2$s. Got: %s',
                static::valueToString($value),
                static::valueToString($subString)
            ));
        }
    }

    public static function startsWith($value, $prefix)
    {
        if (0 !== strpos($value, $prefix)) {
            static::badArgument(sprintf(
                'Expected a value to start with %2$s. Got: %s',
                static::valueToString($value),
                static::valueToString($prefix)
            ));
        }
    }

    public static function startsWithLetter($value)
    {
        $valid = isset($value[0]);

        if ($valid) {
            $locale = setlocale(LC_CTYPE, 0);
            setlocale(LC_CTYPE, 'C');
            $valid = ctype_alpha($value[0]);
            setlocale(LC_CTYPE, $locale);
        }

        if (!$valid) {
            static::badType('a value to start with a letter', $value);
        }
    }

    public static function endsWith($value, $suffix)
    {
        if ($suffix !== substr($value, -static::strlen($suffix))) {
            static::badArgument(sprintf(
                'Expected a value to end with %2$s. Got: %s',
                static::valueToString($value),
                static::valueToString($suffix)
            ));
        }
    }

    public static function regex($value, $pattern)
    {
        if (!preg_match($pattern, $value)) {
            static::badArgument(sprintf(
                'The value %s does not match the expected pattern.',
                static::valueToString($value)
            ));
        }
    }

    public static function alpha($value)
    {
        $locale = setlocale(LC_CTYPE, 0);
        setlocale(LC_CTYPE, 'C');
        $valid = !ctype_alpha($value);
        setlocale(LC_CTYPE, $locale);

        if ($valid) {
            static::badType('a value to contain only letters', $value);
        }
    }

    public static function digits($value)
    {
        $locale = setlocale(LC_CTYPE, 0);
        setlocale(LC_CTYPE, 'C');
        $valid = !ctype_digit($value);
        setlocale(LC_CTYPE, $locale);

        if ($valid) {
            static::badType('a value to contain digits only', $value);
        }
    }

    public static function alnum($value)
    {
        $locale = setlocale(LC_CTYPE, 0);
        setlocale(LC_CTYPE, 'C');
        $valid = !ctype_alnum($value);
        setlocale(LC_CTYPE, $locale);

        if ($valid) {
            static::badType('a value to contain letters and digits only', $value);
        }
    }

    public static function lower($value)
    {
        $locale = setlocale(LC_CTYPE, 0);
        setlocale(LC_CTYPE, 'C');
        $valid = !ctype_lower($value);
        setlocale(LC_CTYPE, $locale);

        if ($valid) {
            static::badType('a value to contain lowercase characters only', $value);
        }
    }

    public static function upper($value)
    {
        $locale = setlocale(LC_CTYPE, 0);
        setlocale(LC_CTYPE, 'C');
        $valid = !ctype_upper($value);
        setlocale(LC_CTYPE, $locale);

        if ($valid) {
            static::badType('a value to contain uppercase characters only', $value);
        }
    }

    public static function length($value, $length)
    {
        if ($length !== static::strlen($value)) {
            static::badArgument(sprintf(
                'Expected a value to contain %2$s characters. Got: %s',
                static::valueToString($value),
                $length
            ));
        }
    }

    public static function minLength($value, $min)
    {
        if (static::strlen($value) < $min) {
            static::badArgument(sprintf(
                'Expected a value to contain at least %2$s characters. Got: %s',
                static::valueToString($value),
                $min
            ));
        }
    }

    public static function maxLength($value, $max)
    {
        if (static::strlen($value) > $max) {
            static::badArgument(sprintf(
                'Expected a value to contain at most %2$s characters. Got: %s',
                static::valueToString($value),
                $max
            ));
        }
    }

    public static function lengthBetween($value, $min, $max)
    {
        $length = static::strlen($value);

        if ($length < $min || $length > $max) {
            static::badArgument(sprintf(
                'Expected a value to contain between %2$s and %3$s characters. Got: %s',
                static::valueToString($value),
                $min,
                $max
            ));
        }
    }

    public static function fileExists($value)
    {
        static::string($value);

        if (!file_exists($value)) {
            static::badArgument(sprintf(
                'The file %s does not exist.',
                static::valueToString($value)
            ));
        }
    }

    public static function file($value)
    {
        static::fileExists($value);

        if (!is_file($value)) {
            static::badArgument(sprintf(
                'The path %s is not a file.',
                static::valueToString($value)
            ));
        }
    }

    public static function directory($value)
    {
        static::fileExists($value);

        if (!is_dir($value)) {
            static::badArgument(sprintf(
                'The path %s is no directory.',
                static::valueToString($value)
            ));
        }
    }

    public static function readable($value)
    {
        if (!is_readable($value)) {
            static::badArgument(sprintf(
                'The path %s is not readable.',
                static::valueToString($value)
            ));
        }
    }

    public static function writable($value)
    {
        if (!is_writable($value)) {
            static::badArgument(sprintf(
                'The path %s is not writable.',
                static::valueToString($value)
            ));
        }
    }

    public static function classExists($value)
    {
        if (!class_exists($value)) {
            static::badArgument(sprintf(
                'Expected an existing class name. Got: %s',
                static::valueToString($value)
            ));
        }
    }

    public static function subclassOf($value, $class)
    {
        if (!is_subclass_of($value, $class)) {
            static::badArgument(sprintf(
                'Expected a sub-class of %2$s. Got: %s',
                static::valueToString($value),
                static::valueToString($class)
            ));
        }
    }

    public static function implementsInterface($value, $interface)
    {
        if (!in_array($interface, class_implements($value))) {
            static::badArgument(sprintf(
                'Expected an implementation of %2$s. Got: %s',
                static::valueToString($value),
                static::valueToString($interface)
            ));
        }
    }

    public static function propertyExists($classOrObject, $property)
    {
        if (!property_exists($classOrObject, $property)) {
            static::badArgument(sprintf(
                'Expected the property %s to exist.',
                static::valueToString($property)
            ));
        }
    }

    public static function propertyNotExists($classOrObject, $property)
    {
        if (property_exists($classOrObject, $property)) {
            static::badArgument(sprintf(
                'Expected the property %s to not exist.',
                static::valueToString($property)
            ));
        }
    }

    public static function methodExists($classOrObject, $method)
    {
        if (!method_exists($classOrObject, $method)) {
            static::badArgument(sprintf(
                'Expected the method %s to exist.',
                static::valueToString($method)
            ));
        }
    }

    public static function methodNotExists($classOrObject, $method)
    {
        if (method_exists($classOrObject, $method)) {
            static::badArgument(sprintf(
                'Expected the method %s to not exist.',
                static::valueToString($method)
            ));
        }
    }

    public static function keyExists($array, $key)
    {
        if (!array_key_exists($key, $array)) {
            static::badArgument(sprintf(
                'Expected the key %s to exist.',
                static::valueToString($key)
            ));
        }
    }

    public static function keyNotExists($array, $key)
    {
        if (array_key_exists($key, $array)) {
            static::badArgument(sprintf(
                'Expected the key %s to not exist.',
                static::valueToString($key)
            ));
        }
    }

    public static function count($array, $number)
    {
        static::eq(count($array), $number);
    }
    
    public static function uuid($value)
    {
        $value = str_replace(array('urn:', 'uuid:', '{', '}'), '', $value);

        // The nil UUID is special form of UUID that is specified to have all
        // 128 bits set to zero.
        if ('00000000-0000-0000-0000-000000000000' === $value) {
            return;
        }

        if (!preg_match('/^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/', $value)) {
            static::badArgument(sprintf(
                'Value %s is not a valid UUID.',
                static::valueToString($value)
            ));
        }
    }

    protected static function valueToString($value)
    {
        if (null === $value) {
            return 'null';
        }
        if (true === $value) {
            return 'true';
        }
        if (false === $value) {
            return 'false';
        }
        if (is_array($value)) {
            return 'array';
        }
        if (is_object($value)) {
            return get_class($value);
        }
        if (is_resource($value)) {
            return 'resource';
        }
        if (is_string($value)) {
            return '"'.$value.'"';
        }
        return (string) $value;
    }

    protected static function typeToString($value)
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }

    protected static function strlen($value)
    {
        if (!function_exists('mb_detect_encoding')) {
            return strlen($value);
        }
        if (false === $encoding = mb_detect_encoding($value)) {
            return strlen($value);
        }
        return mb_strwidth($value, $encoding);
    }

    static function badType($value, $type)
    {
        self::badArgument(
            'Expected ' . $type . '. Got: ' . static::valueToString($value)
        );
    }
    
    static function badArgument($message)
    {
        throw new BadArgumentException($message); 
    }     
}