<?php

namespace RickDBCN\FilamentEmail;

use Filament\Contracts\Plugin;
use Filament\Panel;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource;

class FilamentEmailPlugin implements Plugin
{
    public function getId(): string
    {
        return FilamentEmailServiceProvider::$name;
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            EmailResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {

    }
}
