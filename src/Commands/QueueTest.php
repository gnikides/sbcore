<?php namespace Core\Commands;

use Illuminate\Console\Command;

class QueueTest extends Command
{
    protected $signature    = 'story:queue';
    protected $description  = 'Test queue working';
 
    public function handle()
    {
        $this->info("Testing queue ... "); 
        \App\Jobs\QueueTest::dispatch(); 
    }
}