<?php

namespace RickDBCN\FilamentEmail;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Config;
use RickDBCN\FilamentEmail\Models\Email;
use RickDBCN\FilamentEmail\Providers\EmailMessageServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasMigrations([
                'create_filament_email_table',
                'add_attachments_field_to_filament_email_log_table',
            ]);

        $this->app->register(EmailMessageServiceProvider::class);
    }

    public function bootingPackage()
    {
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $modelClass = Config::get('filament-email.resource.model') ?? Email::class;
            $class = get_class(new $modelClass);
            if (class_exists($class)) {
                $schedule->command('model:prune --model="'.$class.'"')->daily();
            }
        });
    }
}
