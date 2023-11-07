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
use Exception;


class EmailResource extends Resource
{
    protected static ?string $model = Email::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $slug = 'emails';
    
    public static function getNavigationLabel(): string
    {
        try{
            $str =Config::get('filament-email.label');
            if($str == null){
             return  __('Email log');
            }else{
             return  __($str);
            }
        }catch(Exception  $ex){
            return  __('Email log');
        }
    }

    public static function getNavigationGroup(): ?string
    {
        try{
            $str =Config::get('filament-email.resource.group');
            if($str == null){
             return parent::getNavigationGroup();
            }else{
             return  __($str);
            }
        }catch(Exception  $ex){
            return  parent::getNavigationGroup();
        }
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
                    ->label(__('Preview'))
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
                    ->label(__('Send again'))
                    ->icon('heroicon-o-envelope')
                    ->action(function (Email $record) {
                        try {
                            Mail::to($record->to)
                                ->cc($record->cc)
                                ->bcc($record->bcc)
                                ->send(new ResendMail($record));
                            Notification::make()
                                ->title(__('E-mail has been successfully sent'))
                                ->success()
                                ->duration(5000)
                                ->send();
                        } catch (\Exception) {
                            Notification::make()
                                ->title(__('Something went wrong'))
                                ->danger()
                                ->duration(5000)
                                ->send();
                        }
                    }),
            ])
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('Date and time sent'))
                    ->dateTime()
                    ->icon('heroicon-m-calendar')
                    ->sortable(),
                TextColumn::make('from')
                    ->label(__('From'))
                    ->icon('heroicon-m-envelope')
                    ->searchable(),
                TextColumn::make('to')
                    ->label(__('To'))
                    ->icon('heroicon-m-envelope')
                    ->searchable(),
                TextColumn::make('subject')
                    ->label(__('Subject'))
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
