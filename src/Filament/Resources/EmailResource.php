<?php

namespace RickDBCN\FilamentEmail\Filament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\View;
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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages\ListEmails;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages\ViewEmail;
use RickDBCN\FilamentEmail\Mail\ResendMail;
use RickDBCN\FilamentEmail\Models\Email;

class EmailResource extends Resource
{
    protected static ?string $slug = 'emails';

    public static function getBreadcrumb(): string
    {
        return __('filament-email::filament-email.email_log');
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Envelope')
                    ->label('')
                    ->schema([
                        TextInput::make('from')
                            ->label(__('filament-email::filament-email.from'))
                            ->columnSpan(2),
                        Textinput::make('to')
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
                    Tabs\Tab::make(__('filament-email::filament-email.html'))
                        ->schema([
                            ViewField::make('html_body')
                                ->label('')
                                ->view('filament-email::html_view'),
                        ]),
                    Tabs\Tab::make(__('filament-email::filament-email.text'))
                        ->schema([
                            Textarea::make('text_body')
                                ->label('')
                                ->rows(20),
                        ]),
                    Tabs\Tab::make(__('filament-email::filament-email.raw'))
                        ->schema([
                            ViewField::make('raw_body')
                                ->label('')
                                ->view('filament-email::raw_body'),
                        ]),
                    Tabs\Tab::make(__('filament-email::filament-email.debug_info'))
                        ->schema([
                            Textarea::make('sent_debug_info')
                                ->label('')
                                ->rows(20),
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
                        fn ($action): array => [
                            $action->getModalCancelAction(),
                        ])
                    ->fillForm(function ($record) {
                        $body = $record->html_body;

                        return [
                            'html_body' => $body,
                        ];
                    })
                    ->form([
                        ViewField::make('html_body')
                            ->hiddenLabel()
                            ->view('filament-email::html_view'),
                    ]),
                Action::make('resend')
                    ->label(false)
                    ->icon('heroicon-o-arrow-path')
                    ->iconSize(IconSize::Medium)
                    ->tooltip(__('filament-email::filament-email.resend_email_heading'))
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
                        } catch (\Exception $e) {
                            Log::error($e->getMessage());
                            Notification::make()
                                ->title(__('filament-email::filament-email.resend_email_error'))
                                ->danger()
                                ->duration(5000)
                                ->send();
                        }
                    }),
                Action::make('resend-mod')
                    ->label(false)
                    ->icon('heroicon-o-envelope-open')
                    ->iconSize(IconSize::Medium)
                    ->tooltip(__('filament-email::filament-email.update_and_resend_email_heading'))
                    ->modalHeading(__('filament-email::filament-email.update_and_resend_email_heading'))
                    ->form([
                        TagsInput::make('to')
                            ->label(__('filament-email::filament-email.to'))
                            ->placeholder(__('filament-email::filament-email.insert_multiple_email_placelholder'))
                            ->nestedRecursiveRules([
                                'email',
                            ])
                            ->default(fn ($record): array => ! empty($record->to) ? explode(',', $record->to) : [])
                            ->required(),
                        TagsInput::make('cc')
                            ->label(__('filament-email::filament-email.cc'))
                            ->placeholder(__('filament-email::filament-email.insert_multiple_email_placelholder'))
                            ->nestedRecursiveRules([
                                'email',
                            ])
                            ->default(fn ($record): array => ! empty($record->cc) ? explode(',', $record->cc) : []),
                        TagsInput::make('bcc')
                            ->label(__('filament-email::filament-email.bcc'))
                            ->placeholder(__('filament-email::filament-email.insert_multiple_email_placelholder'))
                            ->nestedRecursiveRules([
                                'email',
                            ])
                            ->default(fn ($record): array => ! empty($record->bcc) ? explode(',', $record->bcc) : []),
                        Toggle::make('attachments')
                            ->label(__('filament-email::filament-email.add_attachments'))
                            ->onColor('success')
                            ->offColor('danger')
                            ->inline(false)
                            ->disabled(fn ($record): bool => empty($record->attachments))
                            ->default(fn ($record): bool => ! empty($record->attachments))
                            ->required(),
                    ])
                    ->action(function (Email $record, array $data) {
                        try {
                            Mail::to($data['to'])
                                ->cc($data['cc'])
                                ->bcc($data['bcc'])
                                ->send(new ResendMail($record, $data['attachments'] ?? false));
                            Notification::make()
                                ->title(__('filament-email::filament-email.resend_email_success'))
                                ->success()
                                ->duration(5000)
                                ->send();
                        } catch (\Exception $e) {
                            Log::error($e->getMessage());
                            Notification::make()
                                ->title(__('filament-email::filament-email.resend_email_error'))
                                ->danger()
                                ->duration(5000)
                                ->send();
                        }
                    })
                    ->modalWidth('2xl'),
            ])
            ->columns([
                TextColumn::make('from')
                    ->prefix(__('filament-email::filament-email.from').': ')
                    ->suffix(fn (Email $record): string => ! empty($record->attachments) ? ' ('.trans_choice('filament-email::filament-email.attachments_number', count($record->attachments)).')' : '')
                    ->label(__('filament-email::filament-email.header'))
                    ->description(fn (Email $record): string => Str::limit(__('filament-email::filament-email.to').': '.$record->to, 40))
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
                            TextInput::make('to')
                                ->label(__('filament-email::filament-email.to'))
                                ->email(),
                            TextInput::make('cc')
                                ->label(__('filament-email::filament-email.cc'))
                                ->email(),
                            TextInput::make('bcc')
                                ->label(__('filament-email::filament-email.bcc'))
                                ->email(),
                            DateTimePicker::make('created_from')
                                ->label(__('filament-email::filament-email.from_filter'))
                                ->native(false)
                                ->firstDayOfWeek(1)
                                ->displayFormat(config('filament-email.resource.filter_date_format'))
                                ->time(false),
                            DateTimePicker::make('created_until')
                                ->label(__('filament-email::filament-email.to_filter'))
                                ->native(false)
                                ->firstDayOfWeek(1)
                                ->displayFormat(config('filament-email.resource.filter_date_format'))
                                ->time(false),
                        ];
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        $format = config('filament-email.resource.filter_date_format');

                        if (! empty($data['created_from'])) {
                            $from = Carbon::parse($data['created_from'])->format($format);
                            $indicators['created'] = __('filament-email::filament-email.from_filter')." $from";
                        }

                        if (! empty($data['created_until'])) {
                            $to = Carbon::parse($data['created_until'])->format($format);
                            $toText = __('filament-email::filament-email.to_filter');
                            if (! empty($indicators['created'])) {
                                $indicators['created'] .= ' '.strtolower($toText)." $to";
                            } else {
                                $indicators['created'] = "$toText $to";
                            }

                        }

                        unset($data['created_from']);
                        unset($data['created_until']);

                        foreach ($data as $field => $value) {
                            if ($data[$field] ?? null) {
                                $indicators[$field] = $value;
                            }
                        }

                        return $indicators;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['to'],
                                fn (Builder $query, $value): Builder => $query->where('to', 'like', "%$value%"),
                            )
                            ->when(
                                $data['cc'],
                                fn (Builder $query, $value): Builder => $query->where('cc', 'like', "%$value%"),
                            )
                            ->when(
                                $data['bcc'],
                                fn (Builder $query, $value): Builder => $query->where('bcc', 'like', "%$value%"),
                            )
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $value): Builder => $query->where('created_at', '>=', $value),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $value): Builder => $query->where('created_at', '<=', $value),
                            );
                    }),
            ])
            ->paginationPageOptions(function (Table $table) {
                $options = config('filament-email.pagination_page_options');

                return ! empty($options) && is_array($options) ? $options : $table->getPaginationPageOptions();
            });
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
        $roles = config('filament-email.can_access.role', []);

        if (method_exists(auth()->user(), 'hasRole') && ! empty($roles)) {
            return auth()->user()->hasRole($roles);
        }

        return true;
    }
}
