<?php namespace App\Modules\ProductSearch;

use App\Modules\ProductSearch\Configurator;

trait SearchTrait
{
    protected $indexConfigurator = Configurator::class;
    protected $searchRules = [
        //
    ];

    //  Elastic search mapping
    protected $mapping = [
        'properties' => [
            /* Integers */
            'category_id' => [
                'type' => 'keyword'
            ],
            'site_id' => [
                'type' => 'keyword'
            ],
            'gross_price' => [
                'type' => 'keyword'
            ],
            'net_price' => [
                'type' => 'keyword'
            ],
            'average_rating' => [
                'type' => 'integer'
            ],
            'number_ratings' => [
                'type' => 'integer'
            ],
            /* Dates */
            'updated_at' => [
                'type'      => 'date',
                'format'    => 'yyyy-MM-dd HH:mm:ss'
            ],
            /* Keywords */
            'handle' => [
                'type' => 'keyword',
                'index'     => false,
                'copy_to'   => 'search_text'
            ],
            'country_code' => [
                'type' => 'keyword'
            ],

            /* Search text, unindexed */
            'category_name' => [
                'type'      => 'text',
                'index'     => false,
                'copy_to'   => 'search_text'
            ],
            'site_name' => [
                'type'      => 'text',
                'index'     => false,
                'copy_to'   => 'search_text'
            ],
            'country' => [
                'type'      => 'text',
                'index'     => false,
                'copy_to'   => 'search_text'
            ],
            'descriptions' => [
                'type'      => 'text',
                'index'     => false,
                'copy_to'   => 'search_text',
            ],
            'attributes' => [
                'type'      => 'text',
                'index'     => false,
                'copy_to'   => 'search_text',
            ],
            'features' => [
                'type'      => 'text',
                'index'     => false,
                'copy_to'   => 'search_text',
            ],
            /* Search text, indexed */
            'product_name' => [
                 //  use "fields" to create 2 types of mapping, analyzed full-text & alphabetical
                 'type'      => 'text',
                 'analyzer'  => 'standard',
                 'copy_to'   => 'search_text',
                 'index'     => true,
                 'fields'    => [
                    'raw'   => [
                        //  enables alphabetical sorting
                        'type'      => 'text',
                        'analyzer'  => 'keyword_lowercase_analyzer',
                        'fielddata' => true
                    ],
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
            'manufacturer' => [
                 //  use "fields" to create 2 types of mapping, analyzed full-text & alphabetical
                 'type'      => 'text',
                 'analyzer'  => 'standard',
                 'copy_to'   => 'search_text',
                 'index'     => true,
                 'fields'    => [
                    'raw'   => [
                        //  enables alphabetical sorting
                        'type'      => 'text',
                        'analyzer'  => 'keyword_lowercase_analyzer',
                        'fielddata' => true
                    ],
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
            'sku' => [
                 //  use "fields" to create 2 types of mapping, analyzed full-text & alphabetical
                 'type'      => 'text',
                 'analyzer'  => 'standard',
                 'copy_to'   => 'search_text',
                 'index'     => true,
                 'fields'    => [
                    'raw'   => [
                        //  enables alphabetical sorting
                        'type'      => 'text',
                        'analyzer'  => 'keyword_lowercase_analyzer',
                        'fielddata' => true
                    ],
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
            'creator' => [
                'type'      => 'text',
                'analyzer'  => 'standard',
                'copy_to'   => 'search_text',
            ],
            'publisher_reference' => [
                'type'      => 'text',
                'analyzer'  => 'standard',
                'copy_to'   => 'search_text',
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
            //  the product version in json form
            'content' => [
                'type'      => 'text',
                'index'     => false,
            ] 
        ]
    ];
}    