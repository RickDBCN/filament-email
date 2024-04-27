<?php

use Faker\Factory;
use Illuminate\Support\Facades\Mail;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages\ListEmails;
use RickDBCN\FilamentEmail\Models\Email;
use RickDBCN\FilamentEmail\Tests\Models\User;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertModelExists;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    $this->model = config('filament-email.resource.model') ?? Email::class;
    $this->actingAs(User::factory()->create());
});

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

    assertEquals($this->model::first()->to, $recipient);
});

it('can render table page', function () {
    $this->model::factory()->create();
    livewire(ListEmails::class)->assertSuccessful();
});

it('can resend email', function () {
    $email = $this->model::factory()->create();
    livewire(ListEmails::class)
        ->callTableAction('resend', $email);
    assertDatabaseCount((new $this->model)->getTable(), 2);
});
