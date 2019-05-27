<?php namespace Core\ProductSearch\Configurator;

class Helper
{
    public static function resolveConfigurator(string $locale)
    {
        switch ($locale) {
            case 'fr_FR':
            case 'fr':
                $configurator = \Core\ProductSearch\Configurator\Fr::class;
            break;
            case 'en_US':
            case 'us':
                $configurator = \Core\ProductSearch\Configurator\Us::class;
            break;
            default:
                $configurator = \Core\ProductSearch\Configurator\Us::class;
        }
        return $configurator;
    }
}
