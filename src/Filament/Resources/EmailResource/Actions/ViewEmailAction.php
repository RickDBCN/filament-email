<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Actions;

use Filament\Forms\Components\ViewField;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Filament\Support\Enums\IconSize;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;
use RickDBCN\FilamentEmail\Mail\ResendMail;
use Filament\Actions\Concerns\CanCustomizeProcess;

class ViewEmailAction extends Action
{
    use CanCustomizeProcess;

    private string $filePath;

    public static function getDefaultName(): ?string
    {
        return 'view_email_action';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(false)
            ->icon('heroicon-o-eye')
            ->iconSize(IconSize::Medium)
            ->modalFooterActions(
                fn($action): array => [
                    $action->getModalCancelAction(),
                ])
            ->fillForm(function ($record) {
                $body = $record->html_body;
                return [
                    'html_body' => $body,
                ];
            })
            ->form([
                ViewField::make('html_body')
                    ->hiddenLabel()
                    ->view('filament-email::html_view'),
            ]);
    }
}
