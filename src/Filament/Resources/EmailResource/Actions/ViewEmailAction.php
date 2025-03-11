<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\ViewField;
use Filament\Support\Enums\IconSize;
use Filament\Tables\Actions\Action;

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
                fn ($action): array => [
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
