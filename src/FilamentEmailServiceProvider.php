<?php

namespace RickDBCN\FilamentEmail;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Routing\Router;
use RickDBCN\FilamentEmail\Http\Middleware\PostmarkMiddleware;
use RickDBCN\FilamentEmail\Providers\EmailMessageServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('PostmarkMiddleware', PostmarkMiddleware::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }


}
