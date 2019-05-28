<?php namespace Core\ProductSearch\Configurator;

class Factory
{
    public static function make(string $locale)
    {
        switch ($locale) {
            case 'fr-FR':
            case 'fr':
                $model = \App\Models\ProductVersion\Eu::class;
            break;
            case 'en-US':
            case 'us':
                $model = \App\Models\ProductVersion\Us::class;
            break;
            default:
                $model = \App\Models\ProductVersion\Eu::class;
        }
        return $model;
    }
}
