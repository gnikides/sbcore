<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearCache extends Command
{
    protected $signature = 'story:clear';
    protected $description = 'Clear all caches';

    public function handle()
    {  
        $files = [
            base_path() . '/bootstrap/cache/packages.php',
            base_path() . '/bootstrap/cache/routes.php',
            base_path() . '/bootstrap/cache/services.php',
            base_path() . '/storage/framework/sessions/*',
            base_path() . '/storage/framework/cache/*',
            base_path() . '/storage/framework/views/*'
        ];
        foreach ($files as $file) {
            exec(escapeshellcmd('rm -f ' . $file));
            $this->info("$file removed!");
        }          
        $this->call('clear-compiled');
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('view:clear');
        exec(escapeshellcmd('composer clear-cache'));
        exec(escapeshellcmd('composer dump-autoload'));
        $this->call('optimize'); 
        $this->call('route:cache');
        $this->call('api:cache');
    }
}
