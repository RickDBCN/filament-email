<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Actions;

use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TagsInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconSize;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RickDBCN\FilamentEmail\Mail\ResendMail;
use RickDBCN\FilamentEmail\Models\Email;

class AdvancedResendEmailAction extends Action
{
    use CanCustomizeProcess;

    private string $filePath;

    public static function getDefaultName(): ?string
    {
        return 'advanced_resend_email_action';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(false)
            ->icon('heroicon-o-envelope-open')
            ->iconSize(IconSize::Medium)
            ->tooltip(__('filament-email::filament-email.update_and_resend_email_heading'))
            ->modalHeading(__('filament-email::filament-email.update_and_resend_email_heading'))
            ->modalWidth('2xl')
            ->schema([
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
                Radio::make('attachments')
                    ->label(__('filament-email::filament-email.add_attachments'))
                    ->boolean()
                    ->inline()
                    ->inlineLabel(false)
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
            });
    }
}
