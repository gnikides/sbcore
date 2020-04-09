<?php namespace Core\Search\Order;

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
            'order_id' => [
                'type' => 'keyword',
                'index' => true,
                'copy_to' => 'search_text'
            ],
            'store_id' => [
                'type' => 'keyword'
            ],          
            'status' => [
                'type' => 'keyword'
            ],                         
            'pay_status' => [
                'type' => 'keyword'
            ], 
            'pay_brand' => [
                'type' => 'keyword',
                'copy_to' => 'search_text'  
            ],                        
            'ship_status' => [
                'type' => 'keyword'
            ],           
            'total' => [
                'type' => 'keyword',
                'copy_to' => 'search_text'
            ],
            'customer_id' => [
                'type' => 'keyword'
            ],                                     
            'email' => [
                'type' => 'keyword',
                'index' => true,
                'copy_to' => 'search_text'                
            ],
            'full_name' => [
                'type' => 'keyword',
                'index' => true,
                'copy_to' => 'search_text'                
            ],
            'number_items' => [
                'type' => 'integer'
            ],
            'updated_at' => [
                'type'      => 'date',
                'format'    => 'yyyy-MM-dd HH:mm:ss'
            ],
            'country_code' => [
                'type' => 'keyword',
                'copy_to' => 'search_text'  
            ],
            /* Keywords */
            'country_name' => [
                'type' => 'keyword',
                'index' => false,
                'copy_to' => 'search_text'
            ],
            'shipping_address' => [
                'type' => 'text',
                'index' => false,
                'copy_to' => 'search_text'
            ],            
            'product' => [
                'type' => 'text',
                'index' => false,
                'copy_to' => 'search_text'
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
            //  the product in json form
            'content' => [
                'type'      => 'text',
                'index'     => false,
            ]
        ]
    ];
}
