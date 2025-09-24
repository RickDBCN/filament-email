<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Actions;

use Filament\Actions\BulkAction;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconSize;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RickDBCN\FilamentEmail\Mail\ResendMail;

class ResendEmailBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    private string $filePath;

    public static function getDefaultName(): ?string
    {
        return 'resend_email_bulk_action';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-email::filament-email.resend_email_heading'))
            ->icon('heroicon-o-arrow-path')
            ->color('primary')
            ->iconSize(IconSize::Medium)
            ->tooltip(__('filament-email::filament-email.resend_email_heading'))
            ->requiresConfirmation()
            ->modalHeading(__('filament-email::filament-email.resend_email_heading'))
            ->modalDescription(__('filament-email::filament-email.resend_email_description'))
            ->modalIconColor('warning')
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection $records) {
                try {
                    foreach ($records as $record) {
                        Mail::to($record->to)
                            ->cc($record->cc)
                            ->bcc($record->bcc)
                            ->send(new ResendMail($record));
                    }
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
            });
    }
}
