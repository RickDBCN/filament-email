<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource;
use RickDBCN\FilamentEmail\Support\Utils;

class ListEmails extends ListRecords
{
    protected static string $resource = EmailResource::class;

    public function getTitle(): string
    {
        return __('filament-email::filament-email.emails.list.title');
    }

    public function getHeading(): string
    {
        return __('filament-email::filament-email.emails.list.heading');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('filament-email::filament-email.emails.list.subheading');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('integrations-button')
                ->label(__('filament-email::filament-email.emails.list.redirect-button.label'))
                ->icon(__('filament-email::filament-email.emails.list.redirect-button.icon'))
                ->url(Utils::getIntegrationResourceSlug())
                ->visible(fn () => Utils::isIntegrationsButtonEnabled()),
            //            Action::make('button')
            //                ->label(__('filament-email::filament-email.list.button.label'))
            //                ->icon(__('filament-email::filament-email.list.button.icon'))
            //                ->visible(fn () => Utils::isModalButtonEnabled())
            //                ->modalHeading(__('filament-email::filament-email.modal.heading'))
            //                ->modalDescription(__('filament-email::filament-email.modal.subheading'))
            //                ->modalSubmitActionLabel(__('filament-email::filament-email.modal.submit-action-label'))
            //                ->modalCancelActionLabel(__('filament-email::filament-email.modal.cancel-action-label'))
            //                ->fillForm([
            //                    __('filament-email::filament-email.modal.fields.field1.label') => config('filament-email.modal.field1.value'),
            //                    __('filament-email::filament-email.modal.fields.field2.label') => config('filament-email.modal.field2.value'),
            //                    __('filament-email::filament-email.modal.fields.field3.label') => config('filament-email.modal.field3.value'),
            //                    __('filament-email::filament-email.modal.fields.field4.label') => config('filament-email.modal.field4.value'),
            //                    __('filament-email::filament-email.modal.fields.field5.label') => config('filament-email.modal.field5.value'),
            //                    __('filament-email::filament-email.modal.fields.field6.label') => config('filament-email.modal.field6.value'),
            //                    __('filament-email::filament-email.modal.fields.field7.label') => config('filament-email.modal.field7.value'),
            //                    __('filament-email::filament-email.modal.fields.field8.label') => config('filament-email.modal.field8.value'),
            //                    __('filament-email::filament-email.modal.fields.field9.label') => config('filament-email.modal.field9.value'),
            //                ])
            //                ->form([
            //                    Grid::make(config('filament-email.modal.columns'))
            //                        ->schema([
            //                            TextInput::make(__('filament-email::filament-email.modal.fields.field1.label'))
            //                                ->label(__('filament-email::filament-email.modal.fields.field1.label'))
            //                                ->visible(config('filament-email.modal.field1.visible')),
            //                            TextInput::make(__('filament-email::filament-email.modal.fields.field2.label'))
            //                                ->label(__('filament-email::filament-email.modal.fields.field2.label'))
            //                                ->visible(config('filament-email.modal.field2.visible')),
            //                            TextInput::make(__('filament-email::filament-email.modal.fields.field3.label'))
            //                                ->label(__('filament-email::filament-email.modal.fields.field3.label'))
            //                                ->visible(config('filament-email.modal.field3.visible')),
            //                            TextInput::make(__('filament-email::filament-email.modal.fields.field4.label'))
            //                                ->label(__('filament-email::filament-email.modal.fields.field4.label'))
            //                                ->visible(config('filament-email.modal.field4.visible')),
            //                            TextInput::make(__('filament-email::filament-email.modal.fields.field5.label'))
            //                                ->label(__('filament-email::filament-email.modal.fields.field5.label'))
            //                                ->visible(config('filament-email.modal.field5.visible')),
            //                            TextInput::make(__('filament-email::filament-email.modal.fields.field6.label'))
            //                                ->label(__('filament-email::filament-email.modal.fields.field6.label'))
            //                                ->visible(config('filament-email.modal.field6.visible')),
            //                            TextInput::make(__('filament-email::filament-email.modal.fields.field7.label'))
            //                                ->label(__('filament-email::filament-email.modal.fields.field7.label'))
            //                                ->visible(config('filament-email.modal.field7.visible')),
            //                            TextInput::make(__('filament-email::filament-email.modal.fields.field8.label'))
            //                                ->label(__('filament-email::filament-email.modal.fields.field8.label'))
            //                                ->visible(config('filament-email.modal.field8.visible')),
            //                            TextInput::make(__('filament-email::filament-email.modal.fields.field9.label'))
            //                                ->label(__('filament-email::filament-email.modal.fields.field9.label'))
            //                                ->visible(config('filament-email.modal.field9.visible')),
            //                        ]),
            //                ])
            //                ->action(function (array $data): void {
            //
            //                }),
        ];
    }

    public function getTabs(): array
    {
        $array = [];

        if (! is_null(config('filament-email.status'))) {
            foreach (config('filament-email.status') as $tab => $status) {
                $array[$tab] = $tab;
            }

            foreach (config('filament-email.status') as $tab => $status) {
                $array[$tab] = Tab::make($tab)
                    ->label(ucfirst($tab))
                    ->icon($status['icon'] ?? null)
                    ->modifyQueryUsing($status['query'] ?? fn (Builder $query) => $query);
            }

        }

        return $array;
    }

}
