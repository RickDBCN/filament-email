<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource;

class ViewEmail extends ViewRecord
{
    public static function getResource(): string
    {
        return config('filament-email.resource.class', EmailResource::class);
    }
}
