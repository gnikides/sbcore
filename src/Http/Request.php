<?php namespace Core\Http;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Response;

abstract class Request extends FormRequest
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }

    public function response(array $errors, array $headers = [])
    {         
        $data['error']['field_errors'] = $errors;
        return Response::json(
            $data,
            '400',
            $headers
        );
    }
 
    protected function failedValidation(Validator $validator)
    { 
        $errors = $this->formatErrors($validator);
        throw new HttpResponseException(response()->json($errors, 422));
    }

    protected function formatErrors(Validator $validator)
    {
        $errors = []; 
        $failed = $validator->failed();
        if ($failed) {            
            $messages = $validator->getMessageBag()->toArray();            
            foreach ($failed as $field => $code) {                
                $message = isset($messages[$field][0]) ? $messages[$field][0] : '';                
                //  get first key in $code array
                reset($code);        
                $errors[] = [                       
                    'message'               => $message,
                    'field'                 => $field,
                    'code'                  => key($code),
                    'user_message_title'    => trans('Field Error'), 
                    'user_message'          => trans($message),
                    'time'                  => now()                                     
                ];
            }
        }
        return $errors;
    }

    public function isCreate()
    {         
        return ('POST' == $this->method()); 
    }

    public function isUpdate()
    {         
        return ('PUT' == $this->method() || 'PATCH' == $this->method()); 
    }
    
    public function validator($factory)
    {
        return $factory->make(
            $this->replace($this->sanitize($this->all()))->all(),
            $this->container->call([$this, 'rules']),
            $this->messages()
        );
    }
    
    public function sanitize($input)
    {
        return $input;
    }

    // public function sanitizeString($value)
    // {
    //     return filter_var($value, FILTER_SANITIZE_STRING);
    // }

    // public function sanitizeInt($value)
    // {
    //     return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    // }

    // public function sanitizeFloat($value)
    // {
    //     return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
    // }

    public function sanitizeArray(array $input = [], array $filters = [])
    {
        $output = [];
        foreach ($filters as $key => $filter) {
            if (array_key_exists($key, $input)) {
                if ('int' == $filter) {
                    $output[$key] = sanitizeInt($input[$key]);
                } elseif ('string' == $filter) {
                    $output[$key] = sanitizeString($input[$key]);
                } elseif ('strtolower' == $filter) {
                    $output[$key] = strtolower(sanitizeString($input[$key]));
                } elseif ('strtoupper' == $filter) {
                    $output[$key] = strtoupper(sanitizeString($input[$key]));
                } elseif ('active' == $filter) {
                    $output[$key] = strtolower(sanitizeString(array_get($input, $key, 'active')));
                } elseif ('meta' == $filter || 'string_array' == $filter) {
                    if (is_array($input[$key])) {
                        foreach ($input[$key] as $k => $v) {
                            $output[$key][strtolower(sanitizeString($k))] = sanitizeString($v);
                        }
                    }
                } elseif ('integer_array' == $filter) {
                    if (is_array($input[$key])) {
                        foreach ($input[$key] as $k => $v) {
                            $output[$key][strtolower(sanitizeString($k))] = sanitizeInt($v);
                        }
                    }                                         
                } elseif ('none' == $filter) {
                    $output[$key] = $input[$key];
                }
            } else {
                if ('active' == $filter) {
                    $output[$key] = strtolower(sanitizeString(array_get($input, $key, 'active')));
                }
            }    
        }
        return $output;
    }    
}
