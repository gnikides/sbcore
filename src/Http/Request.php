<?php namespace Core\Http;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;
use Response;

abstract class Request extends FormRequest
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const CREATE = 'create';
    const UPDATE = 'update';

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

    public function withValidator($validator)
    {  
        if (!$validator->fails()) {
            $validator->after(function($validator) {    
                $this->replace($this->formatInput($this->all()))->all();      
            });
        }    
    }

    public function formatInput($input)
    {
        return $input;
    }  
    
    public function removeNonNumeric($value)
    {
        return preg_replace('/[^0-9.]+/', '', $value);
    }
      
    public static function getRules()
    {
        $class = static::class;
        $instance = new $class;
        return $instance->rules();  
    }
}
