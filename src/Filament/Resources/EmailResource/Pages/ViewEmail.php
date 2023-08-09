<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource;

class ViewEmail extends ViewRecord
{
    protected static string $resource = EmailResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            $this->getResource()::getUrl() => __('filament-email::filament-email.emails.list.heading'),
            '#' => __('filament-email::filament-email.emails.view.breadcrumb'),
        ];
    }

    public function getTitle(): string
    {
        return __('filament-email::filament-email.emails.view.title');
    }

    public function getHeading(): string
    {
        return __('filament-email::filament-email.emails.view.heading');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('filament-email::filament-email.emails.view.subheading');
    }
}
