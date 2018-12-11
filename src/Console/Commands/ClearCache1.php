<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearCache extends Command
{
    protected $signature = 'story:clear';
    protected $description = 'Clear all caches';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        //$this->call('view:clear');

        exec(escapeshellcmd('composer clear-cache'));
        exec(escapeshellcmd('composer dump-autoload'));          
        $files = [
            base_path() . '/bootstrap/cache/routes.php',
            base_path() . '/bootstrap/cache/services.json',
            base_path() . '/storage/framework/sessions/*',
            base_path() . '/storage/framework/cache/*',
            //base_path() . '/storage/framework/views/*'
        ];                
        foreach ($files as $file) {
            exec(escapeshellcmd('rm -f ' . $file));
            echo "$file removed!\n";
        }
        $this->call('optimize');        
        $this->call('route:cache');
        $this->call('config:cache');
    }
}
