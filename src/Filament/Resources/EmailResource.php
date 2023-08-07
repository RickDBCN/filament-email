<?php

namespace RickDBCN\FilamentEmail\Filament\Resources;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Config;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages\ListEmails;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages\ViewEmail;
use RickDBCN\FilamentEmail\Models\Email;

class EmailResource extends Resource
{
    protected static ?string $model = Email::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $slug = 'emails';

    public static function getNavigationLabel(): string
    {
        return Config::get('filament-email.label') ?? __('Email log');
    }

    public static function getNavigationGroup(): ?string
    {
        return Config::get('filament-email.resource.group' ?? parent::getNavigationGroup());
    }

    public static function getNavigationSort(): ?int
    {
        return Config::set('filament-email.resource.sort') ?? parent::getNavigationSort();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Envelope')
                    ->label('')
                    ->schema([
                    TextInput::make('created_at')
                        ->label(__('Created at')),
                    TextInput::make('from')
                        ->label(__('From')),
                    Textinput::make('to')
                        ->label(__('To')),
                    TextInput::make('cc')
                        ->label(__('CC')),
                    TextInput::make('subject')
                        ->label(__('Subject'))
                        ->columnSpan(2),
                ])->columns(3),
                Tabs::make('Content')->tabs([
                    Tabs\Tab::make('HTML')
                        ->schema([
                            Textarea::make('html_body')
                        ]),
                    Tabs\Tab::make('Text')
                        ->schema([
                            Textarea::make('text_body'),
                        ]),
                    Tabs\Tab::make('raw')
                        ->schema([
                            Textarea::make('raw_body')
                        ]),
                    Tabs\Tab::make('Debug info')
                        ->schema([
                            Textarea::make('sent_debug_info')
                        ]),
                ])->columnSpan(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('Date and time sent'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('from')
                    ->label(__('From'))
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('to')
                    ->label(__('To'))
                    ->searchable(),
                TextColumn::make('cc')
                    ->label(__('Cc'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->limit(50)

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmails::route('/'),
            'view' => ViewEmail::route('/{record}'),
        ];
    }
}
