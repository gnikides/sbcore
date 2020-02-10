<?php namespace Core\Support;

class Rules
{
    const ID                = 'integer';
    const ID_REQUIRED       = 'required|integer';
    const NAME              = 'max:255';
    const DESCRIPTION       = 'max:10000';
    const META              = 'nullable|array';
    const ACTIVE_INACTIVE   = 'in:active,inactive';
    const SORT_ORDER        = 'numeric|max:9999';
    const COUNTRY_CODE      = 'string|min:2|max:2';
    const CURRENCY_CODE     = 'string|min:3|max:3';
    const LOCALE            = 'string|min:2|max:10';
    const PRICE             = 'numeric';    
    const DAY_OF_MONTH      = 'numeric|between:1,31';
    const MONTH_AS_DIGIT    = 'numeric|between:1,12';
    const REQUIRED_DATE     = 'required|date';

    public static function money(int $max_digits = 15)
    {
        return 'regex:/^(\d+(?:[\.\,]\d{2})?)$/|between:0,' . (!$max_digits ? 8 : $max_digits);
    }
}
