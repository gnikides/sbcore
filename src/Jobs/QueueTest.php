<?php namespace Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class QueueTest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
            
    public function __construct()
    {
    }

    public function handle()
    { 
        \Log::info('queue_test', ['Queue is working']);
    }
    
    public function failed(Exception $exception)
    {
    }   
}
