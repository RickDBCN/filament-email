<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconSize;
use Filament\Tables\Actions\BulkAction;
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

    /**
     * Parse email addresses in RFC 5322 format to an array of Address objects
     */
    private function parseEmailAddresses(?string $addresses): array
    {
        if (empty($addresses)) {
            return [];
        }

        $result = [];
        $parts = explode(',', $addresses);

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) {
                continue;
            }

            // Use Symfony's Address::create() to parse the formatted string
            try {
                $result[] = \Symfony\Component\Mime\Address::create($part);
            } catch (\Exception $e) {
                // If parsing fails, skip this address
                Log::warning("Failed to parse email address: $part");
            }
        }

        return $result;
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
                        Mail::to($this->parseEmailAddresses($record->to))
                            ->cc($this->parseEmailAddresses($record->cc))
                            ->bcc($this->parseEmailAddresses($record->bcc))
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
