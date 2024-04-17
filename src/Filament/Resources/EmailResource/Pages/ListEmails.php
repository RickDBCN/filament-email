<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource;

class ListEmails extends ListRecords
{
    public static function getResource(): string
    {
        return config('filament-email.resource.class', EmailResource::class);
    }

    protected function applySearchToTableQuery(Builder $query): Builder
    {
        if (filled($searchQuery = $this->getTableSearch())) {
            return $query->filter(['search' => $searchQuery]);
        }

        return $query;
    }
}
