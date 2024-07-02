<?php

namespace RickDBCN\FilamentEmail;

use Filament\Contracts\Plugin;
use Filament\Panel;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource;

class FilamentEmail implements Plugin
{
    public function getId(): string
    {
        return FilamentEmailServiceProvider::$name;
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            config('filament-email.resource.class', EmailResource::class),
        ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
