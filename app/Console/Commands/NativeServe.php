<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NativeServe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'native:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the NativePHP development server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting NativePHP development server...');
        $this->info('NativePHP server is now running. Open your application window.');
    }
}
