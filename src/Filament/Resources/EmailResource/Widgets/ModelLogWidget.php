<?php

namespace RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Widgets;

use Filament\Widgets\Widget;
use RickDBCN\FilamentEmail\Models\Email;

class ModelLogWidget extends Widget
{
    public ?Email $record = null;

    protected int|string|array $columnSpan = 'full';
}
