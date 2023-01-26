<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class DeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launch:deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This script is launched every time it is deployed.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Artisan::call('optimize:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        if (config('app.env') === 'production') {
            $this->info('Application In Production!. Do you really wish to run migrate command? (yes/no) [no]');
            Artisan::call('migrate');
        } else {
            Artisan::call('migrate');
        }

        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('clear-compiled');
        Artisan::call('queue:restart');
        // Artisan::call('queue:clear');


        $this->optimizeApp();
        $this->info('✅ ' . 'Scripts launched successfully');
    }

    /**
     * Method optimizeApp
     *
     * @return void
     */
    private function optimizeApp()
    {
        try {
            if (config('app.env') === 'production' || config('app.env') === 'develop') {
                // composer install --optimize-autoloader --no-dev
                Artisan::call('optimize');
                Artisan::call('config:cache');
                Artisan::call('route:cache');
                Artisan::call('view:cache');
            }
        } catch (\Exception $e) {
            $this->error('⭕ ' . $e->getMessage());
            Log::error('⭕ ' . $e->getMessage());
            return;
        }

        $this->info('✅ ' . 'Ready app optimization');
    }
}
