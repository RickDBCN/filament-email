<?php

namespace RickDBCN\FilamentEmail\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RickDBCN\FilamentEmail\FilamentEmail
 */
class FilamentEmail extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \RickDBCN\FilamentEmail\FilamentEmail::class;
    }
}
