<?php

namespace RickDBCN\FilamentEmail\Providers;

use Illuminate\Events\EventServiceProvider;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use RickDBCN\FilamentEmail\Listeners\FilamentEmailLogger;

class EmailMessageServiceProvider extends EventServiceProvider
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
