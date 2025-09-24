<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Size;
use Illuminate\Support\Facades\Storage;
use RickDBCN\FilamentEmail\Filament\Resources\Actions\NextAction;
use RickDBCN\FilamentEmail\Filament\Resources\Actions\PreviousAction;
use RickDBCN\FilamentEmail\Filament\Resources\Concernes\CanPaginateViewRecord;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource;
use RickDBCN\FilamentEmail\Models\Email;

class ViewEmail extends ViewRecord
{
    use CanPaginateViewRecord;

    public Email $email;

    public static function getResource(): string
    {
        return config('filament-email.resource.class', EmailResource::class);
    }

    public function downloadAction(): Action
    {
        return Action::make('download')
            ->label(__('filament-email::filament-email.download'))
            ->requiresConfirmation()
            ->icon('heroicon-c-arrow-down-tray')
            ->size(Size::ExtraSmall)
            ->action(function (array $arguments) {
                $fileExists = Storage::disk(config('filament-email.attachments_disk'))->exists($arguments['path']);
                if ($fileExists) {
                    return Storage::disk(config('filament-email.attachments_disk'))->download($arguments['path'], $arguments['name']);
                } else {
                    Notification::make()
                        ->title(__('filament-email::filament-email.download_attachment_error'))
                        ->danger()
                        ->duration(5000)
                        ->send();
                }
            });
    }

    protected function getHeaderActions(): array
    {
        return [
            PreviousAction::make(),
            NextAction::make(),
        ];
    }
}
