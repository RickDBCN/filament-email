<?php

namespace RickDBCN\FilamentEmail;

use Illuminate\Support\Facades\Config;
use Spatie\LaravelPackageTools\Package;
use RickDBCN\FilamentEmail\Models\Email;
use Illuminate\Console\Scheduling\Schedule;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use RickDBCN\FilamentEmail\Providers\EmailMessageServiceProvider;

class FilamentEmailServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-email';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('filament-email')
            ->hasConfigFile('filament-email')
            ->hasTranslations()
            ->hasViews()
            ->hasMigration('create_filament_email_table');

        $this->app->register(EmailMessageServiceProvider::class);
    }

    public function bootingPackage()
    {
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $modelClass = Config::get('filament-email.resource.model') ?? Email::class;
            $class = get_class(new $modelClass);
            if (class_exists($class)) {
                $schedule->command('model:prune --model="' . $class . '"')->daily();
            }
        });
    }
}
