<?php

namespace RickDBCN\FilamentEmail\Filament\Resources;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages\ListEmails;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages\ViewEmail;
use RickDBCN\FilamentEmail\Mail\ResendMail;
use RickDBCN\FilamentEmail\Models\Email;
use RickDBCN\FilamentEmail\Support\Utils;

class EmailResource extends Resource
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Envelope')
                    ->label('')
                    ->schema([
                        TextInput::make('created_at')
                            ->label(__('filament-email::filament-email.form.field.created_at')),
                        TextInput::make('from')
                            ->label(__('filament-email::filament-email.form.field.from')),
                        Textinput::make('to')
                            ->label(__('filament-email::filament-email.form.field.to')),
                        TextInput::make('cc')
                            ->label(__('filament-email::filament-email.form.field.cc')),
                        TextInput::make('subject')
                            ->label(__('filament-email::filament-email.form.field.subject'))
                            ->columnSpan(2),
                    ])->columns(3),
                Tabs::make('Content')->tabs([
                    Tabs\Tab::make('HTML')
                        ->schema([
                            ViewField::make('html_body')->hiddenLabel()
                                ->view('filament-email::filament-email.emails.html')->view('filament-email::HtmlEmailView'),
                        ]),
                    Tabs\Tab::make('Text')
                        ->schema([
                            Textarea::make('text_body'),
                        ]),
                    Tabs\Tab::make('Raw')
                        ->schema([
                            Textarea::make('raw_body'),
                        ]),
                    Tabs\Tab::make('Debug info')
                        ->schema([
                            Textarea::make('sent_debug_info'),
                        ]),
                ])->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('filament-email::filament-email.table.column.created_at'))
                    ->dateTime()
                    ->icon('heroicon-m-calendar')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('filament-email::filament-email.table.column.status'))
                    ->badge()
                    ->toggleable()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->colors(function () {
                        $array = [];
                        if (! is_null(config('filament-email.status'))) {
                            foreach (config('filament-email.status') as $tab => $status) {
                                $array[$status['color'] ?? 'gray'] = $tab;
                            }

                            foreach (config('filament-email.status') as $tab => $status) {
                                $array[$status['color'] ?? 'gray'] = $tab;
                            }
                        }
                        return $array;
                    })
                    ->sortable(),
                TextColumn::make('subject')
                    ->label(__('filament-email::filament-email.table.column.subject'))
                    ->icon('heroicon-m-chat-bubble-bottom-center')
                    ->limit(50),
                TextColumn::make('from')
                    ->label(__('filament-email::filament-email.table.column.from'))
                    ->icon('heroicon-m-envelope')
                    ->searchable(),
                TextColumn::make('to')
                    ->label(__('filament-email::filament-email.table.column.to'))
                    ->icon('heroicon-m-envelope')
                    ->searchable(),
                TextColumn::make('cc')
                    ->label(__('filament-email::filament-email.table.column.cc'))
                    ->icon('heroicon-m-envelope')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-m-eye')
                    ->extraAttributes(['style' => 'h-41'])
                    ->modalFooterActions(
                        fn ($action): array =>
                        [
                        $action->getModalCancelAction(),
                    ])
                    ->fillForm(function (Email $record) {
                        $body = $record->html_body;

                        return [
                            'html_body' => $body,
                        ];
                    })
                    ->form([
                        ViewField::make('html_body')->hiddenLabel()
                            ->view('filament-email::filament-email.emails.html')->view('filament-email::HtmlEmailView'),
                    ]),
                Action::make('resend')
                    ->label(__('filament-email::filament-email.table.action.send-again'))
                    ->icon('heroicon-m-envelope')
                    ->action(function (Email $record) {
                        try {
                            Mail::to($record->to)
                                ->cc($record->cc)
                                ->bcc($record->bcc)
                                ->send(new ResendMail($record));
                            Notification::make()
                                ->title(__('filament-email::filament-email.table.action.send-again.success.title'))
                                ->success()
                                ->duration(5000)
                                ->send();
                        } catch (\Exception $exception) {
                            Notification::make()
                                ->title(__('filament-email::filament-email.table.action.send-again.error.title'))
                                ->body($exception)
                                ->danger()
                                ->duration(5000)
                                ->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmails::route('/'),
            'view' => ViewEmail::route('/{record}'),
        ];
    }

    protected static ?string $model = Email::class;

    public static function shouldRegisterNavigation(): bool
    {
        return Utils::isEmailResourceNavigationRegistered();
    }

    public static function getSlug(): string
    {
        return Utils::getEmailResourceSlug();
    }

    public static function getNavigationSort(): ?int
    {
        return Utils::getEmailResourceNavigationSort();
    }

    public static function getNavigationGroup(): ?string
    {
        return Utils::isEmailResourceNavigationGroupEnabled()
            ? __('filament-email::filament-email.emails.nav.group')
            : '';
    }

    public static function getNavigationIcon(): ?string
    {
        return __('filament-email::filament-email.emails.nav.icon');
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return __('filament-email::filament-email.emails.nav.active_icon');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-email::filament-email.emails.nav.label');
    }
}
