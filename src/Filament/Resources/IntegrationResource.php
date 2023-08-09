<?php

namespace RickDBCN\FilamentEmail\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use RickDBCN\FilamentEmail\Filament\Resources\IntegrationResource\Pages\ListIntegrations;
use RickDBCN\FilamentEmail\Filament\Resources\IntegrationResource\Pages\ViewIntegration;
use RickDBCN\FilamentEmail\Models\Email;
use RickDBCN\FilamentEmail\Models\Integration;
use RickDBCN\FilamentEmail\Support\Utils;

class IntegrationResource extends Resource
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('created_at')
                    ->label(__('filament-email::filament-email.form.field.created_at')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIntegrations::route('/'),
            'view' => ViewIntegration::route('/{record}'),
        ];
    }

    protected static ?string $model = Integration::class;

    public static function shouldRegisterNavigation(): bool
    {
        return Utils::isIntegrationResourceNavigationRegistered();
    }

    public static function getSlug(): string
    {
        return Utils::getIntegrationResourceSlug();
    }

    public static function getNavigationSort(): ?int
    {
        return Utils::getIntegrationResourceNavigationSort();
    }

    public static function getNavigationGroup(): ?string
    {
        return Utils::isIntegrationResourceNavigationGroupEnabled()
            ? __('filament-email::filament-email.integrations.nav.group')
            : '';
    }

    public static function getNavigationIcon(): ?string
    {
        return __('filament-email::filament-email.integrations.nav.icon');
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return __('filament-email::filament-email.integrations.nav.active_icon');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-email::filament-email.integrations.nav.label');
    }

}
