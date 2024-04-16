<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages;

use Filament\Resources\Pages\ListRecords;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource;

class ListEmails extends ListRecords
{
    public static function getResource(): string
    {
        return config('filament-email.resource.class', EmailResource::class);
    }
}
