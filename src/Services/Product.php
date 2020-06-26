<?php namespace Core\Services;

class Product
{
    public static function identityFields()
    {
        return [
            'sku' => [
                'name' => 'sku',
                'label' => 'SKU',
                'placeholder' => 'XXXXXXXXX'
            ],
            'mpn' => [
                'name' => 'mpn',
                'label' => 'MPN',
                'placeholder' => 'MPN'
            ],
            'gtin' => [
                'name' => 'gtin',
                'label' => 'GTIN',
                'placeholder' => 'Global Trade Item Number'
            ],
            'asin' => [
                'name' => 'asin',
                'label' => 'ASIN',
                'placeholder' => 'Amazon Standard ID Number'
            ],
            // 'custom' => [
            //     'name' => 'custom',
            //     'label' => 'Product Reference',
            //     'placeholder' => ''
            // ]
        ];    
    }

    public static function parcelFields()
    {
        return [
            'length' => [
                'name' => 'length',
                'label' => 'Length',
                'placeholder' => '10'
            ],
            'height' => [
                'name' => 'height',
                'label' => 'Height',
                'placeholder' => '10'
            ], 
            'width' => [
                'name' => 'width',
                'label' => 'Width',
                'placeholder' => '10'
            ], 
            'size_unit' => [
                'name' => 'size_unit',
                'label' => 'Size Unit',
                'placeholder' => 'cm'
            ], 
            'weight' => [
                'name' => 'weight',
                'label' => 'Weight',
                'placeholder' => '10'
            ], 
            'weight_unit' => [
                'name' => 'weight_unit',
                'label' => 'Weight Unit',
                'placeholder' => 'kg'
            ],                                                           
        ];    
    }

    public static function descriptionFields()
    {
        return [
            'main' => [
                'name' => 'length',
                'label' => 'Length',
                'placeholder' => '10'
            ],
            'short' => [
                'name' => 'height',
                'label' => 'Height',
                'placeholder' => '10'
            ], 
            'editorial_review' => [
                'name' => 'width',
                'label' => 'Width',
                'placeholder' => '10'
            ]                                                         
        ];    
    }    
}
