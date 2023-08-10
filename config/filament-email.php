<?php

// config for RickDBCN/FilamentEmail
return [
    'resources' => [
        'emails' => [
            'should_register_navigation' => true,
            'slug' => 'emails',
            'in_navigation_group' => true,
            'navigation_sort' => 1,
            'show_integrations_button' => true,
        ],
        'integrations' => [
            'should_register_navigation' => true,
            'slug' => 'integrations',
            'in_navigation_group' => true,
            'navigation_sort' => 2,
        ],
    ],
    'modal' => [
        'show_modal_button' => true,
        'columns' => 3,
        'field1' => [
            'value' => env('MAIL_MAILER'),
            'visible' => true,
        ],
        'field2' => [
            'value' => env('MAIL_HOST'),
            'visible' => true,
        ],
        'field3' => [
            'value' => env('MAIL_PORT'),
            'visible' => true,
        ],
        'field4' => [
            'value' => env('MAIL_USERNAME'),
            'visible' => true,
        ],
        'field5' => [
            'value' => env('MAIL_PASSWORD'),
            'visible' => true,
        ],
        'field6' => [
            'value' => env('MAIL_ENCRYPTION'),
            'visible' => true,
        ],
        'field7' => [
            'value' => env('MAIL_FROM_ADDRESS'),
            'visible' => true,
        ],
        'field8' => [
            'value' => env('MAIL_FROM_NAME'),
            'visible' => true,
        ],
        'field9' => [
            'value' => env('POSTMARK_TOKEN'),
            'visible' => true,
        ],
    ],
    'status' => [
        'all' => [
            'icon' => 'heroicon-o-table-cells',
        ],
        'delivered' => [
            'icon' => 'heroicon-o-envelope-open',
            'color' => 'success',
            'query' => fn (Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'delivered'),
        ],
        'sent' => [
            'icon' => 'heroicon-o-envelope',
            'color' => 'warning',
            'query' => fn (Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'sent'),
        ],
        'failed' => [
            'icon' => 'heroicon-o-x-circle',
            'color' => 'danger',
            'query' => fn (Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'failed'),
        ],
    ],
    'keep_email_for_days' => 60,
];
