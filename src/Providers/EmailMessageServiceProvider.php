<?php

namespace RickDBCN\FilamentEmail\Providers;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use RickDBCN\FilamentEmail\Listeners\FilamentEmailLogger;

class EmailMessageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(
            MessageSent::class,
            [FilamentEmailLogger::class, 'handle']
        );
    }
    //    protected $listen = [
    //        MessageSent::class => [
    //            FilamentEmailLogger::class,
    //        ],
    //    ];

}
