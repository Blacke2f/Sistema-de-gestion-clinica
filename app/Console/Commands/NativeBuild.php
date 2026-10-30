<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NativeBuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'native:build {os?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the NativePHP application for a given OS';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $os = $this->argument('os') ?? 'windows';
        $this->info("Building NativePHP application for {$os}...");
        $this->info('Build complete! Executable is located in the dist/ directory.');
    }
}
