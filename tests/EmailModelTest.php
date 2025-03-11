<?php

use Faker\Factory;
use Illuminate\Support\Facades\Mail;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Actions\AdvancedResendEmailAction;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Actions\ResendEmailAction;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Actions\ResendEmailBulkAction;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Actions\ViewEmailAction;
use RickDBCN\FilamentEmail\Filament\Resources\EmailResource\Pages\ListEmails;
use RickDBCN\FilamentEmail\Models\Email;
use RickDBCN\FilamentEmail\Tests\Models\User;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertModelExists;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    $this->model = config('filament-email.resource.model') ?? Email::class;
    $this->actingAs(User::factory()
        ->create());
});

it('can create an Email model', function () {
    $emailModel = Email::factory()
        ->create();
    assertModelExists($emailModel);
});

it('can capture a sent email', function () {
    $faker = Factory::create();
    $recipient = $faker->email();

    Mail::raw('Test e-mail text', function ($message) use ($recipient) {
        $message->to($recipient)
            ->subject('the email subject');
    });

    assertDatabaseCount((new $this->model)->getTable(), 1);

    assertEquals($this->model::first()->to, $recipient);
});

it('can render table page', function () {
    $this->model::factory()
        ->create();
    livewire(ListEmails::class)
        ->assertSuccessful();
});

it('can render table records', function () {
    $records = $this->model::factory()
        ->count(10)
        ->create();
    livewire(ListEmails::class)
        ->assertCanSeeTableRecords($records);
});

it('can view email', function () {
    $email = $this->model::factory()
        ->create();
    livewire(ListEmails::class)
        ->callTableAction(ViewEmailAction::class, $email)
        ->assertSuccessful();
});

it('can resend email', function () {
    $email = $this->model::factory()
        ->create();
    livewire(ListEmails::class)
        ->callTableAction(ResendEmailAction::class, $email);
    assertDatabaseCount((new $this->model)->getTable(), 2);
});

it('can advanced resend email', function () {
    $email = $this->model::factory()
        ->create();
    livewire(ListEmails::class)
        ->setTableActionData([
            'to' => explode(',', $email->to),
            'cc' => explode(',', $email->cc),
            'bcc' => explode(',', $email->bcc),
            'attachments' => true,
        ])
        ->callTableAction(AdvancedResendEmailAction::class, $email);
    assertDatabaseCount((new $this->model)->getTable(), 2);
});

it('can bulk resend email', function () {
    $email = $this->model::factory()
        ->create();
    livewire(ListEmails::class)
        ->callTableBulkAction(ResendEmailBulkAction::class, [$email]);
    assertDatabaseCount((new $this->model)->getTable(), 2);
});
