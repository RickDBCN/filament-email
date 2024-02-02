<?php

// config for RickDBCN/FilamentEmail
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource;
use RickDBCN\FilamentEmail\Models\Email;

return [

    'resource' => [
        'class' => EmailResource::class,
        'model' => Email::class,
        'group' => null,
        'sort' => null,
        'default_sort_column' => 'created_at',
        'default_sort_direction' => 'desc',
    ],

    'keep_email_for_days' => 60,
    'label' => null,
];
