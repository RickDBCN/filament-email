<?php

namespace RickDBCN\FilamentEmail\Filament\Resources;

use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Actions\AdvancedResendEmailAction;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Actions\ResendEmailAction;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Actions\ResendEmailBulkAction;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Actions\ViewEmailAction;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages\ListEmails;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages\ViewEmail;
use RickDBCN\FilamentEmail\Models\Email;

class EmailResource extends Resource
{
    protected static ?string $slug = 'emails';

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    /**
     * Determine whether the resource should be scoped to the current tenant.
     */
    public static function isScopedToTenant(): bool
    {
        return config('filament-email.scope_to_tenant', true);
    }

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

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
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
                ViewEmailAction::make(),
                ResendEmailAction::make(),
                AdvancedResendEmailAction::make(),
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
                ResendEmailBulkAction::make(),
                DeleteBulkAction::make()
                    ->requiresConfirmation(),
            ])
            ->persistFiltersInSession()
            ->filters(self::getFilters())
            ->filtersFormWidth(Width::ExtraLarge)
            ->paginationPageOptions(function (Table $table) {
                $options = config('filament-email.pagination_page_options');

                return ! empty($options) && is_array($options) ? $options : $table->getPaginationPageOptions();
            });
    }

    private static function getFilters()
    {
        return [
            Filter::make('headers-filter')
                ->schema([
                    TextInput::make('from')
                        ->label(__('filament-email::filament-email.from'))
                        ->email(),
                    TextInput::make('to')
                        ->label(__('filament-email::filament-email.to'))
                        ->email(),
                    TextInput::make('cc')
                        ->label(__('filament-email::filament-email.cc'))
                        ->email(),
                    TextInput::make('bcc')
                        ->label(__('filament-email::filament-email.bcc'))
                        ->email(),
                    DateRangePicker::make('created_at')
                        ->label(__('filament-email::filament-email.sent_at')),
                    Select::make('attachments')
                        ->label(__('filament-email::filament-email.have_attachments'))
                        ->options([
                            'no' => ucfirst(__('filament-email::filament-email.no')),
                            'yes' => ucfirst(__('filament-email::filament-email.yes')),
                        ]),
                ])
                ->columns(2)
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    foreach ($data as $field => $value) {
                        if ($data[$field] ?? null) {
                            if ($field === 'attachments') {
                                $indicators[$field] = __('filament-email::filament-email.'.$field).': '.($value === 'yes' ? __('filament-email::filament-email.yes') : __('filament-email::filament-email.no'));
                            } else {
                                $indicators[$field] = __('filament-email::filament-email.'.$field).": $value";
                            }
                        }
                    }

                    return $indicators;
                })
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['from'],
                            fn (Builder $query, $value): Builder => $query->where('from', 'like', "%$value%"),
                        )
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
                            $data['attachments'],
                            // JSON_LENGTH
                            fn (Builder $query, $value): Builder => $query->where(DB::raw('JSON_LENGTH(attachments)'), $value === 'yes' ? '>' : '=', 0),
                        )
                        ->when(
                            $data['created_at'],
                            function (Builder $query, $value): Builder {
                                [$start, $end] = explode(' - ', $value);
                                $start = str_replace('/', '-', $start);
                                $end = str_replace('/', '-', $end);

                                return $query->whereBetween('created_at', [
                                    Carbon::createFromTimestamp(strtotime($start))
                                        ->format('Y-m-d'),
                                    Carbon::createFromTimestamp(strtotime($end))
                                        ->format('Y-m-d'),
                                ]);
                            }
                        );
                }),
        ];
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
        $user = auth()->user() ?? null;
        $roles = config('filament-email.can_access.role', []);

        if (! is_null($user) && method_exists($user, 'hasRole') && ! empty($roles)) {
            return $user->hasRole($roles);
        }

        return true;
    }
}
