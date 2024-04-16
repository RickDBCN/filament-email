<?php

namespace MG87\FilamentEmail\Filament\Resources\EmailResource\Pages;

use Filament\Resources\Pages\ListRecords;
use MG87\FilamentEmail\Filament\Resources\EmailResource;

class ListEmails extends ListRecords
{
    public static function getResource(): string
    {
        return config('filament-email.resource.class', EmailResource::class);
    }
}
