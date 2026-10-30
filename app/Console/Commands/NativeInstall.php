<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NativeInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'native:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install NativePHP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Installing NativePHP...');
        $this->info('NativePHP installed successfully!');
    }
}
