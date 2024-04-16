<?php

namespace MG87\FilamentEmail\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MG87\FilamentEmail\FilamentEmail
 */
class FilamentEmail extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \MG87\FilamentEmail\FilamentEmail::class;
    }
}
