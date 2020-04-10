<?php namespace Core\Search\Customer;

trait MappingTrait
{
    protected $searchRules = [
        //
    ];
    protected $mapping = [
        'properties' => [
            'id' => [
                'type' => 'keyword'
            ],
            'customer_group_id' => [
                'type' => 'keyword'
            ],
            'full_name' => [
                'type' => 'keyword',
                'index' => true,
                'copy_to' => 'search_text'                
            ],                                    
            'email' => [
                'type' => 'keyword',
                'index' => true,
                'copy_to' => 'search_text'                
            ],
            'country_code' => [
                'type' => 'keyword',
                'copy_to' => 'search_text'  
            ],
            'country_name' => [
                'type' => 'keyword',
                'index' => false,
                'copy_to' => 'search_text'
            ],
            'updated_at' => [
                'type'      => 'date',
                'format'    => 'yyyy-MM-dd HH:mm:ss'
            ],
            'status' => [
                'type' => 'keyword'
            ],
            'ip' => [
                'type' => 'keyword',
                'copy_to' => 'search_text'                
            ],
            'store_id' => [
                'type' => 'keyword'
            ],                        
            //  aggregate text field for analyzed full-text searching
            'search_text' => [
                'type'      => 'text',
                'index'     => true,
                'analyzer'  => 'standard',
                'fields'    => [
                    'french_standard' => [
                        'type'      => 'text',
                        'analyzer'  => 'french_standard_analyzer'
                    ],
                    'english_standard' => [
                        'type'      => 'text',
                        'analyzer'  => 'english_standard_analyzer'
                    ]
                ]
            ],
            //  the customer in json form
            'content' => [
                'type'      => 'text',
                'index'     => false,
            ]
        ]
    ];
}
