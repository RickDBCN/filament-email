<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\Actions;

use Filament\Actions\Action;

class PreviousAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'previous';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->hiddenLabel()
            ->icon('heroicon-o-arrow-left')
            ->outlined()
            ->size('sm')
            ->tooltip(__('filament-email::filament-email.previous'));
    }
}
