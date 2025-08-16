<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\Emails;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use RickDBCN\FilamentEmail\Filament\Resources\Emails\Schemas\EmailForm;
use RickDBCN\FilamentEmail\Filament\Resources\Emails\Tables\EmailTable;
use RickDBCN\FilamentEmail\Models\Email;

class EmailResource extends Resource
{
    protected static ?string $slug = 'emails';

    public static function getBreadcrumb(): string
    {
        return __('filament-email::filament-email.email_log');
    }

    /**
     * @return class-string<Cluster> | null
     */
    public static function getCluster(): ?string
    {
        return config('filament-email.resource.cluster');
    }

    public static function getNavigationLabel(): string
    {
        return config('filament-email.label') ?? __('filament-email::filament-email.navigation_label');
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return config('filament-email.resource.icon') ?? 'heroicon-o-envelope';
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-email.resource.group') ?? __('filament-email::filament-email.navigation_group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-email.resource.sort') ?? parent::getNavigationSort();
    }

    public static function getModel(): string
    {
        return config('filament-email.resource.model') ?? Email::class;
    }

    public static function hasTitleCaseModelLabel(): bool
    {
        return config('filament-email.resource.has_title_case_model_label', true);
    }

    public static function getModelLabel(): string
    {
        return __('filament-email::filament-email.model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return EmailForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailTable::configure($table);

    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmails::route('/'),
            'view' => Pages\ViewEmail::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = auth()->user() ?? null;
        $roles = config('filament-email.can_access.role', []);

        if (! is_null($user) && method_exists($user, 'hasRole') && ! empty($roles)) {
            return $user->hasRole($roles);
        }

        return true;
    }
}
