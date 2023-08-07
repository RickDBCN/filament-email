<?php

namespace RickDBCN\FilamentEmail;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentEmail implements Plugin
{
    public function getId(): string
    {
        return FilamentEmailServiceProvider::$name;
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            //resource class
        ]);
    }

    public function boot(Panel $panel): void
    {

    }
}
