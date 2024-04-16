<?php

namespace MG87\FilamentEmail;

use Filament\Contracts\Plugin;
use Filament\Panel;
use MG87\FilamentEmail\Filament\Resources\EmailResource;

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

    public function boot(Panel $panel): void
    {

    }
}
