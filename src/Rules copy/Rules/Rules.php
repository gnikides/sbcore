<?php namespace App\Support\Rules;

class Rules
{
    protected $status;

    const SHARD             = 'integer|max:8192';
    const ID                = 'integer|digits_between:1,11';
    const ID_REQUIRED       = 'required|integer|digits_between:1,11';
    const NAME              = 'max:255';
    const DESCRIPTION       = 'max:10000';
    const METADATA          = 'max:65000';
    const ACTIVE_INACTIVE   = 'in:active,inactive';
    const SORT_ORDER        = 'numeric|max:9999';
    const COUNTRY_CODE      = 'string|min:2|max:2';
    const CURRENCY_CODE     = 'string|min:3|max:3';
    const PRICE             = 'max:25';

    const CREATE    = 'create';
    const UPDATE    = 'update';
    const CREATED   = 'created';
    const UPDATED   = 'updated';
    const DELETED   = 'deleted';

    const DAY_OF_MONTH      = 'numeric|between:1,31';
    const MONTH_AS_DIGIT    = 'numeric|between:1,12';

    public function setStatus($status = '')
    {   
        $this->status = $status;
        return $this;
    }

    public function getStatus()
    {   
        return $this->status;
    }

    public function isUpdate()
    {   
        return (self::UPDATE == $this->getStatus()); 
    }

    public function setCreated()
    {   
        return $this->setStatus(self::CREATED); 
    } 

    public function setUpdate()
    {   
        return $this->setStatus(self::UPDATE); 
    } 

    public function setCreate()
    {   
        return $this->setStatus(self::CREATE); 
    } 

    public function asCreated()
    {   
        return $this->setStatus(self::CREATED); 
    } 

    public function isCreated()
    {   
        return (self::CREATED == $this->getStatus()); 
    }     
}