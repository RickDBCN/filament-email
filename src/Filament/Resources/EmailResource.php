<?php

namespace RickDBCN\FilamentEmail\Filament\Resources;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages\ListEmails;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages\ViewEmail;
use RickDBCN\FilamentEmail\Mail\ResendMail;
use RickDBCN\FilamentEmail\Models\Email;

class EmailResource extends Resource
{
    protected static ?string $model = Email::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $slug = 'emails';

    public static function getNavigationLabel(): string
    {
        return __('email-log.label') == null ? 'Email log' : __('email-log.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('email-log.group') == null ? null : __('email-log.group');
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
                            ->label(__('email-log.created_at')),
                        TextInput::make('from')
                            ->label(__('email-log.from')),
                        Textinput::make('to')
                            ->label(__('email-log.to')),
                        TextInput::make('cc')
                            ->label(__('email-log.cc')),
                        TextInput::make('subject')
                            ->label(__('email-log.subject'))
                            ->columnSpan(2),
                    ])->columns(3),
                Tabs::make('Content')->tabs([
                    Tabs\Tab::make('HTML')
                        ->schema([
                            ViewField::make('html_body')
                                ->view('filament-email::filament-email.emails.html')
                                ->view('filament-email::HtmlEmailView'),
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
            ->defaultSort(config('filament-email.resource.default_sort_column'), config('filament-email.resource.default_sort_direction'))
            ->actions([
                Action::make('preview')
                    ->label(__('email-log.preview'))
                    ->icon('heroicon-m-eye')
                    ->extraAttributes(['style' => 'h-41'])
                    ->modalFooterActions(
                        fn ($action): array => [
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
                    ->label(__('email-log.send_again'))
                    ->icon('heroicon-o-envelope')
                    ->form([
                        TextInput::make('to')
                            ->label(__('email-log.to'))
                            ->default(fn($record):string => $record->to)
                            ->email()
                            ->required(),
                        TextInput::make('cc')
                           ->label(__('email-log.cc'))
                            ->default(fn($record):string => $record->cc)
                            ->email(),
                        TextInput::make('bcc')
                           ->label(__('email-log.bcc'))
                            ->default(fn($record):string => $record->bcc)
                            ->email(),
                    ])
                    ->action(function (Email $record,array $data) {
                        try {
                            Mail::to($data['to'])
                                ->cc($data['cc'])
                                ->bcc($data['bcc'])
                                ->send(new ResendMail($record));
                            Notification::make()
                                ->title(__('email-log.e-mail-has-been-successfully-sent'))
                                ->success()
                                ->duration(5000)
                                ->send();
                        } catch (\Exception) {
                            Notification::make()
                                ->title(__('email-log.something-went-wrong'))
                                ->danger()
                                ->duration(5000)
                                ->send();
                        }
                    }),
            ])
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('email_log.created_at'))
                    ->dateTime()
                    ->icon('heroicon-m-calendar')
                    ->sortable(),
                TextColumn::make('from')
                    ->label(__('email_log.from'))
                    ->icon('heroicon-m-envelope')
                    ->searchable(),
                TextColumn::make('to')
                    ->label(__('email_log.to'))
                    ->icon('heroicon-m-envelope')
                    ->searchable(),
                TextColumn::make('subject')
                    ->label(__('email_log.subject'))
                    ->icon('heroicon-m-chat-bubble-bottom-center')
                    ->limit(50),

            ])
            ->groupedBulkActions([
                DeleteBulkAction::make()
                    ->requiresConfirmation(),
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
