<?php

namespace Bubka\LaravelAuthenticationLog\Commands;

use Illuminate\Console\Command;
use Bubka\LaravelAuthenticationLog\Models\AuthenticationLog;

class PurgeAuthenticationLogCommand extends Command
{
    /**
     * The name and signature of the console command.
     * 
     * @var string
     */
    public $signature = 'authentication-log:purge';

    /**
     * The console command description.
     * 
     * @var string
     */
    public $description = 'Purge all authentication logs older than the configurable amount of days.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->comment('Clearing authentication log...');

        $deleted = AuthenticationLog::where('login_at', '<', now()->subDays(config('authentication-log.purge'))->format('Y-m-d H:i:s'))->delete();

        $this->info($deleted . ' authentication logs cleared.');
    }
}
