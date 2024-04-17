<?php

namespace MG87\FilamentEmail\Filament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconSize;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use MG87\FilamentEmail\Filament\Resources\EmailResource\Pages\ListEmails;
use MG87\FilamentEmail\Filament\Resources\EmailResource\Pages\ViewEmail;
use MG87\FilamentEmail\Mail\ResendMail;
use MG87\FilamentEmail\Models\Email;

class EmailResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $slug = 'emails';

    public static function getBreadcrumb(): string
    {
        return __('filament-email::filament-email.email_log');
    }

    public static function getNavigationLabel(): string
    {
        return Config::get('filament-email.label') ?? __('filament-email::filament-email.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return Config::get('filament-email.resource.group') ?? __('filament-email::filament-email.navigation_group');
    }

    public static function getNavigationSort(): ?int
    {
        return Config::get('filament-email.resource.sort') ?? parent::getNavigationSort();
    }

    public static function getModel(): string
    {
        return Config::get('filament-email.resource.model') ?? Email::class;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Envelope')
                    ->label('')
                    ->schema([
                        TextInput::make('from')
                            ->label(__('filament-email::filament-email.from')),
                        Textinput::make('to')
                            ->label(__('filament-email::filament-email.to')),
                        TextInput::make('cc')
                            ->label(__('filament-email::filament-email.cc')),
                        TextInput::make('subject')
                            ->label(__('filament-email::filament-email.subject'))
                            ->columnSpan(2),
                        DateTimePicker::make('created_at')
                            ->format(config('filament-email.resource.datetime_format'))
                            ->label(__('filament-email::filament-email.created_at')),
                    ])->columns(3),
                Tabs::make('Content')->tabs([
                    Tabs\Tab::make(__('filament-email::filament-email.html'))
                        ->schema([
                            ViewField::make('html_body')
                                ->label('')
                                ->view('filament-email::filament-email.emails.html')
                                ->view('filament-email::HtmlEmailView'),
                        ]),
                    Tabs\Tab::make(__('filament-email::filament-email.text'))
                        ->schema([
                            Textarea::make('text_body')
                                ->label(''),
                        ]),
                    Tabs\Tab::make(__('filament-email::filament-email.raw'))
                        ->schema([
                            Textarea::make('raw_body')
                                ->label(''),
                        ]),
                    Tabs\Tab::make(__('filament-email::filament-email.debug_info'))
                        ->schema([
                            Textarea::make('sent_debug_info')
                                ->label(''),
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
                    ->label(false)
                    ->icon('heroicon-o-eye')
                    ->iconSize(IconSize::Medium)
                    ->modalFooterActions(
                        fn($action): array => [
                            $action->getModalCancelAction(),
                        ])
                    ->fillForm(function ($record) {
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
                    ->label(false)
                    ->icon('heroicon-o-paper-airplane')
                    ->iconSize(IconSize::Medium)
                    ->requiresConfirmation()
                    ->modalHeading(__('filament-email::filament-email.resend_email_heading'))
                    ->modalDescription(__('filament-email::filament-email.resend_email_description'))
                    ->modalIconColor('warning')
                    ->action(function ($record) {
                        try {
                            Mail::to($record->to)
                                ->cc($record->cc)
                                ->bcc($record->bcc)
                                ->send(new ResendMail($record));
                            Notification::make()
                                ->title(__('filament-email::filament-email.resend_email_success'))
                                ->success()
                                ->duration(5000)
                                ->send();
                        } catch (\Exception) {
                            Notification::make()
                                ->title(__('filament-email::filament-email.resend_email_error'))
                                ->danger()
                                ->duration(5000)
                                ->send();
                        }
                    }),
            ])
            ->columns([
                TextColumn::make('from')
                    ->prefix(__('filament-email::filament-email.from') . ': ')
                    ->label(__('filament-email::filament-email.header'))
                    ->description(fn(Email $record): string => __('filament-email::filament-email.to') . ': ' . $record->to)
                    ->searchable(),
                TextColumn::make('subject')
                    ->label(__('filament-email::filament-email.subject'))
                    ->limit(50),
                TextColumn::make('created_at')
                    ->label(__('filament-email::filament-email.sent_at'))
                    ->dateTime(config('filament-email.resource.datetime_format'))
                    ->sortable(),

            ])
            ->groupedBulkActions([
                DeleteBulkAction::make()
                    ->requiresConfirmation(),
            ])
            ->persistFiltersInSession()
            ->filters([
                Filter::make('created_at')
                    ->form(function () {
                        return [
                            DateTimePicker::make('created_from')
                                ->label(__('filament-email::filament-email.from_filter'))
                                ->native(false)
                                ->firstDayOfWeek(1)
                                ->displayFormat(config('filament-email.resource.filter_date_format'))
                                ->seconds(false)
                                ->time(false),
                            DateTimePicker::make('created_until')
                                ->label(__('filament-email::filament-email.to_filter'))
                                ->native(false)
                                ->firstDayOfWeek(1)
                                ->displayFormat(config('filament-email.resource.filter_date_format'))
                                ->time(false),
                        ];
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['created_from'] && !$data['created_until']) {
                            return null;
                        }
                        $filter = '';
                        $format = config('filament-email.resource.filter_date_format');
                        if (!empty($data['created_from'])) {
                            $from = Carbon::parse($data['created_from'])->format($format);
                            $filter = __('filament-email::filament-email.from_filter') . " $from";
                        }
                        if (!empty($data['created_until'])) {
                            $to = Carbon::parse($data['created_until'])->format($format);
                            $toText = __('filament-email::filament-email.to_filter');
                            $filter .= (!empty($filter) ? ' ' . strtolower($toText) . ' ' : $toText) . "$to";
                        }

                        return $filter;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->where('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->where('created_at', '<=', $date),
                            );
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

    public static function canAccess(): bool
    {
        $roles = config('filament-email.can_access.role') ?? [];

        if (method_exists(auth()->user(), 'hasRole') && !empty($roles)) {
            return auth()->user()->hasRole($roles);
        }

        return true;
    }
}
