<?php

namespace Bubka\LaravelAuthenticationLog;

use Bubka\LaravelAuthenticationLog\Commands\PurgeAuthenticationLogCommand;
use Illuminate\Contracts\Events\Dispatcher;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelAuthenticationLogServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package) : void
    {
        $package
            ->name('laravel-authentication-log')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasMigration('create_authentication_log_table')
            ->hasCommand(PurgeAuthenticationLogCommand::class);

        $events = $this->app->make(Dispatcher::class);

        foreach (config('authentication-log.events', []) as $event => $eventClass) {
            if (class_exists($eventClass) && class_exists(config('authentication-log.listeners.' . $event))) {
                $events->listen(
                    $eventClass,
                    config('authentication-log.listeners.' . $event)
                );
            }
        }
    }
}
