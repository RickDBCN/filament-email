<?php

use Faker\Factory;
use Illuminate\Support\Facades\Mail;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertModelExists;
use function PHPUnit\Framework\assertEquals;
use RickDBCN\FilamentEmail\Models\Email;

it('can create an Email model', function () {
    $emailModel = Email::factory()->create();
    assertModelExists($emailModel);
});

it('can capture a sent email', function () {
    $faker = Factory::create();
    $recipient = $faker->email();

    Mail::raw('Test e-mail text', function ($message) use ($recipient) {
        $message->to($recipient)
            ->subject('the email subject');
    });

    assertDatabaseCount('filament_email_log', 1);

    assertEquals(Email::first()->to, $recipient);
});
