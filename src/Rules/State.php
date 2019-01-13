<?php namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class State implements Rule
{
    public $country_code;
    public $is_state_required;

    public function __construct(string $country_code, bool $is_state_required = false)
    {
        $this->country_code = isset($country_code) ? strtoupper($country_code) : '';
        $this->is_state_required = $is_state_required;
    }

    public function passes($attribute, $value)
    {
        return $this->validate(preg_replace('/\s+/', '', $value));
    }

    public function message()
    {
        return trans('The state is invalid.');
    }

    public function validate($state)
    {        
        return ($this->is_state_required && !$state) ? false : true;
    }
}
