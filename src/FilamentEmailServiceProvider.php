<?php

namespace RickDBCN\FilamentEmail;

use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Blade;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages\ListEmails;
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
                'add_team_id_field_to_filament_email_log_table',
            ]);

        $this->app->register(EmailMessageServiceProvider::class);
    }

    public function bootingPackage()
    {
        FilamentView::registerRenderHook(
            TablesRenderHook::TOOLBAR_SEARCH_BEFORE,
            fn (): string => Blade::render('<x-filament::loading-indicator wire:loading wire:target="previousPage,gotoPage,nextPage" class="ml-3 h-5 w-5" />'),
            scopes: ListEmails::class
        );

        $pruneEnabled = config('filament-email.prune_enabled') ?? false;

        if ($pruneEnabled) {
            $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
                $runCrontab = config('filament-email.prune_crontab', '0 0 * * *');
                $modelClass = config('filament-email.resource.model') ?? Email::class;
                $class = get_class(new $modelClass);
                if (class_exists($class)) {
                    $schedule->command('model:prune --model="'.$class.'"')
                        ->cron($runCrontab);
                }
            });
        }
    }
}
