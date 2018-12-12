<?php namespace Core\Commands;

use Illuminate\Console\Command;

class ClearApc extends Command
{
    protected $signature = 'story:apc';
    protected $description = 'Clear APCu Cache';

    public function handle()
    {
        if (extension_loaded('apc')) {
            echo "APC-User cache " . apc_clear_cache('user') . "cleared\n";
            echo "APC-System cache " . apc_clear_cache() . "cleared\n";
        }
        if (extension_loaded('apcu')) {
            echo "APCu cache " . apcu_clear_cache() . " cleared\n";
        }
        if (function_exists('opcache_reset')) {
            // Clear it twice to avoid some internal issues...
            opcache_reset();
            opcache_reset();
        }
    }
}