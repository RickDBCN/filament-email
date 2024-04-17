<?php

use MG87\FilamentEmail\Filament\Resources\EmailResource;
use MG87\FilamentEmail\Models\Email;

return [

    'resource' => [
        'class' => EmailResource::class,
        'model' => Email::class,
        'group' => null,
        'sort' => null,
        'default_sort_column' => 'created_at',
        'default_sort_direction' => 'desc',
        'datetime_format' => 'Y-m-d H:i:s',
        'filter_date_format' => 'Y-m-d',
    ],

    'keep_email_for_days' => 60,
    'label' => null,

    'can_access' => [
        'role' => [],
    ],
];
