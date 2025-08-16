<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\Emails\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use RickDBCN\FilamentEmail\Filament\Resources\Emails\Actions\AdvancedResendEmailAction;
use RickDBCN\FilamentEmail\Filament\Resources\Emails\Actions\ResendEmailAction;
use RickDBCN\FilamentEmail\Filament\Resources\Emails\Actions\ResendEmailBulkAction;
use RickDBCN\FilamentEmail\Filament\Resources\Emails\Actions\ViewEmailAction;
use RickDBCN\FilamentEmail\Models\Email;

class EmailTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort(config('filament-email.resource.default_sort_column'), config('filament-email.resource.default_sort_direction'))
            ->recordActions([
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

    private static function getFilters(): array
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
}
