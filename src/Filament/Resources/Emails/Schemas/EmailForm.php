<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\Emails\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class EmailForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Envelope')
                    ->label('')
                    ->schema([
                        TextInput::make('from')
                            ->label(__('filament-email::filament-email.from'))
                            ->columnSpan(2),
                        TextInput::make('to')
                            ->label(__('filament-email::filament-email.to'))
                            ->columnSpan(2),
                        TextInput::make('cc')
                            ->label(__('filament-email::filament-email.cc'))
                            ->columnSpan(2),
                        TextInput::make('bcc')
                            ->label(__('filament-email::filament-email.bcc'))
                            ->columnSpan(2),
                        TextInput::make('subject')
                            ->label(__('filament-email::filament-email.subject'))
                            ->columnSpan(3),
                        DateTimePicker::make('created_at')
                            ->format(config('filament-email.resource.datetime_format'))
                            ->label(__('filament-email::filament-email.created_at')),
                    ])->columns(4),
                Fieldset::make('attachments')
                    ->hidden(fn (): bool => ! config('filament-email.store_attachments'))
                    ->label(__('filament-email::filament-email.attachments'))
                    ->schema([
                        View::make('filament-email::attachments')
                            ->columnSpanFull(),
                    ]),
                Tabs::make('Content')->tabs([
                    Tab::make(__('filament-email::filament-email.html'))
                        ->schema([
                            ViewField::make('html_body')
                                ->label('')
                                ->view('filament-email::html_view'),
                        ]),
                    Tab::make(__('filament-email::filament-email.text'))
                        ->schema([
                            Textarea::make('text_body')
                                ->label('')
                                ->rows(20),
                        ]),
                    Tab::make(__('filament-email::filament-email.raw'))
                        ->schema([
                            ViewField::make('raw_body')
                                ->label('')
                                ->view('filament-email::raw_body'),
                        ]),
                    Tab::make(__('filament-email::filament-email.debug_info'))
                        ->schema([
                            Textarea::make('sent_debug_info')
                                ->label('')
                                ->rows(20),
                        ]),
                ])->columnSpan(2),
            ]);
    }
}
