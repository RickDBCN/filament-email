<?php

namespace RickDBCN\FilamentEmail\Commands;

use Illuminate\Console\Command;

class FilamentEmailCommand extends Command
{
    public $signature = 'filament-email';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
