<?php

namespace RickDBCN\FilamentEmail;

use Illuminate\Contracts\Container\BindingResolutionException;
use RickDBCN\FilamentEmail\Http\Middleware\PostmarkMiddleware;
use RickDBCN\FilamentEmail\Providers\EmailMessageServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Illuminate\Routing\Router;

class FilamentEmailServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-email';

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-email')
            ->hasConfigFile('filament-email')
            ->hasTranslations()
            ->hasMigration('create_filament_email_table')
            ->hasViews();

        $this->app->register(EmailMessageServiceProvider::class);
    }

    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('PostmarkMiddleware', PostmarkMiddleware::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}
