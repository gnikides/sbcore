<?php namespace App\Support;

class Rules
{
    const SHARD             = 'integer|max:8192';
    const ID                = 'integer|digits_between:1,11';
    const ID_REQUIRED       = 'required|integer|digits_between:1,11';
    const REQUIRED          = 'required';
    const NAME              = 'max:255';
    const DESCRIPTION       = 'max:10000';
    const METADATA          = 'max:65000';
    const ACTIVE_INACTIVE   = 'in:active,inactive';
    const SORT_ORDER        = 'numeric|max:9999';
    const COUNTRY_CODE      = 'string|min:2|max:2';
    const CURRENCY_CODE     = 'string|min:3|max:3';
    const PRICE             = 'max:25';
    const DAY_OF_MONTH      = 'numeric|between:1,31';
    const MONTH_AS_DIGIT    = 'numeric|between:1,12';     
}