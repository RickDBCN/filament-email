<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Facades\Storage;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource;
use RickDBCN\FilamentEmail\Models\Email;

class ViewEmail extends ViewRecord
{
    use InteractsWithActions;

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
            ->size(ActionSize::ExtraSmall)
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
}
