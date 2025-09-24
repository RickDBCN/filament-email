<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Actions;

use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconSize;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RickDBCN\FilamentEmail\Mail\ResendMail;

class ResendEmailAction extends Action
{
    use CanCustomizeProcess;

    private string $filePath;

    public static function getDefaultName(): ?string
    {
        return 'resend_email_action';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(false)
            ->icon('heroicon-o-arrow-path')
            ->iconSize(IconSize::Medium)
            ->tooltip(__('filament-email::filament-email.resend_email_heading'))
            ->modalHeading(__('filament-email::filament-email.resend_email_heading'))
            ->modalDescription(__('filament-email::filament-email.resend_email_description'))
            ->modalIconColor('warning')
            ->requiresConfirmation()
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
            });
    }
}
