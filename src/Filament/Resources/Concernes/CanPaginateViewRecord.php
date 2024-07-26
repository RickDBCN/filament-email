<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\Concernes;

use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use RickDBCN\FilamentEmail\Filament\Resources\Actions\NextAction;
use RickDBCN\FilamentEmail\Filament\Resources\Actions\PreviousAction;

trait CanPaginateViewRecord
{
    protected function configureAction(Action $action): void
    {
        $this->configureActionRecord($action);

        match (true) {
            $action instanceof PreviousAction => $this->configurePreviousAction($action),
            $action instanceof NextAction => $this->configureNextAction($action),
            default => parent::configureAction($action),
        };
    }

    protected function configurePreviousAction(Action $action): void
    {
        if ($this->getPreviousRecord()) {
            $action->url(fn (): string => static::getResource()::getUrl('view', ['record' => $this->getPreviousRecord()]));
        } else {
            $action
                ->disabled()
                ->color('gray');
        }
    }

    protected function configureNextAction(Action $action): void
    {
        if ($this->getNextRecord()) {
            $action->url(fn (): string => static::getResource()::getUrl('view', ['record' => $this->getNextRecord()]));
        } else {
            $action
                ->disabled()
                ->color('gray');
        }
    }

    protected function getPreviousRecord(): ?Model
    {
        return $this
            ->getRecord()
            ->where('created_at', '<', $this->getRecord()->created_at)
            ->where('id', '<>', $this->getRecord()->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    protected function getNextRecord(): ?Model
    {
        return $this
            ->getRecord()
            ->where('created_at', '>', $this->getRecord()->created_at)
            ->where('id', '<>', $this->getRecord()->id)
            ->orderBy('created_at', 'asc')
            ->first();
    }
}
