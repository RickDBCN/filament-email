<?php

// config for RickDBCN/FilamentEmail
return [
    'resource' => [
        'should_register_navigation' => true,
        'slug' => 'emails',
        'navigation_group' => true,
        'navigation_sort' => 1,
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
    'tabs' => [
      'tab1' => [
          'status' => 'delivered',
          'color' => 'success',
          ],
        'tab2' => [
            'status' => 'sent',
            'color' => 'warning',
        ],
        'tab3' => [
            'status' => 'failed',
            'color' => 'danger',
        ],
    ],

    'keep_email_for_days' => 60,
];
