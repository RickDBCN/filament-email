<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource;
use RickDBCN\FilamentEmail\Support\Utils;

class ListEmails extends ListRecords
{
    protected static string $resource = EmailResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            $this->getResource()::getUrl() => __('filament-email::filament-email.list.heading'),
            '#' => __('filament-email::filament-email.list.breadcrumb'),
        ];
    }

    public function getTitle(): string
    {
        return __('filament-email::filament-email.list.title');
    }

    public function getHeading(): string
    {
        return __('filament-email::filament-email.list.heading');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('filament-email::filament-email.list.subheading');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('button')
                ->label(__('filament-email::filament-email.list.button.label'))
                ->icon(__('filament-email::filament-email.list.button.icon'))
                ->visible(fn () => Utils::isModalButtonEnabled())
                ->modalHeading(__('filament-email::filament-email.modal.heading'))
                ->modalDescription(__('filament-email::filament-email.modal.subheading'))
                ->modalSubmitActionLabel(__('filament-email::filament-email.modal.submit-action-label'))
                ->modalCancelActionLabel(__('filament-email::filament-email.modal.cancel-action-label'))
                ->fillForm([
                    __('filament-email::filament-email.modal.fields.field1.label') => config('filament-email.modal.field1.value'),
                    __('filament-email::filament-email.modal.fields.field2.label') => config('filament-email.modal.field2.value'),
                    __('filament-email::filament-email.modal.fields.field3.label') => config('filament-email.modal.field3.value'),
                    __('filament-email::filament-email.modal.fields.field4.label') => config('filament-email.modal.field4.value'),
                    __('filament-email::filament-email.modal.fields.field5.label') => config('filament-email.modal.field5.value'),
                    __('filament-email::filament-email.modal.fields.field6.label') => config('filament-email.modal.field6.value'),
                    __('filament-email::filament-email.modal.fields.field7.label') => config('filament-email.modal.field7.value'),
                    __('filament-email::filament-email.modal.fields.field8.label') => config('filament-email.modal.field8.value'),
                    __('filament-email::filament-email.modal.fields.field9.label') => config('filament-email.modal.field9.value'),
                ])
                ->form([
                    Grid::make(config('filament-email.modal.columns'))
                        ->schema([
                            TextInput::make(__('filament-email::filament-email.modal.fields.field1.label'))
                                ->label(__('filament-email::filament-email.modal.fields.field1.label'))
                                ->visible(config('filament-email.modal.field1.visible')),
                            TextInput::make(__('filament-email::filament-email.modal.fields.field2.label'))
                                ->label(__('filament-email::filament-email.modal.fields.field2.label'))
                                ->visible(config('filament-email.modal.field2.visible')),
                            TextInput::make(__('filament-email::filament-email.modal.fields.field3.label'))
                                ->label(__('filament-email::filament-email.modal.fields.field3.label'))
                                ->visible(config('filament-email.modal.field3.visible')),
                            TextInput::make(__('filament-email::filament-email.modal.fields.field4.label'))
                                ->label(__('filament-email::filament-email.modal.fields.field4.label'))
                                ->visible(config('filament-email.modal.field4.visible')),
                            TextInput::make(__('filament-email::filament-email.modal.fields.field5.label'))
                                ->label(__('filament-email::filament-email.modal.fields.field5.label'))
                                ->visible(config('filament-email.modal.field5.visible')),
                            TextInput::make(__('filament-email::filament-email.modal.fields.field6.label'))
                                ->label(__('filament-email::filament-email.modal.fields.field6.label'))
                                ->visible(config('filament-email.modal.field6.visible')),
                            TextInput::make(__('filament-email::filament-email.modal.fields.field7.label'))
                                ->label(__('filament-email::filament-email.modal.fields.field7.label'))
                                ->visible(config('filament-email.modal.field7.visible')),
                            TextInput::make(__('filament-email::filament-email.modal.fields.field8.label'))
                                ->label(__('filament-email::filament-email.modal.fields.field8.label'))
                                ->visible(config('filament-email.modal.field8.visible')),
                            TextInput::make(__('filament-email::filament-email.modal.fields.field9.label'))
                                ->label(__('filament-email::filament-email.modal.fields.field9.label'))
                                ->visible(config('filament-email.modal.field9.visible')),
                        ]),
                ])
                ->action(function (array $data): void {

                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            __('filament-email::filament-email.tabs.tab1') => Tab::make()
                ->icon('heroicon-o-table-cells'),
            __('filament-email::filament-email.tabs.tab2') => Tab::make()
                ->icon('heroicon-o-envelope-open')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '=', '2023-08-08 06:42:13')),
            __('filament-email::filament-email.tabs.tab3') => Tab::make()
                ->icon('heroicon-o-envelope')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('active', false)),
            __('filament-email::filament-email.tabs.tab4') => Tab::make()
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('active', false)),
        ];
    }
}
