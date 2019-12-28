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
            'custom' => [
                'name' => 'custom',
                'label' => 'Product Reference',
                'placeholder' => ''
            ]
        ];    
    }
}
