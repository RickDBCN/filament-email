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

it('includes the whole first and last day of the sent at range', function (string $timezone) {
    $default = date_default_timezone_get();
    date_default_timezone_set($timezone);
    config(['app.timezone' => $timezone]);

    try {
        $before = $this->model::factory()
            ->create(['created_at' => '2026-01-31 23:30:00']);
        $first = $this->model::factory()
            ->create(['created_at' => '2026-02-01 00:30:00']);
        $last = $this->model::factory()
            ->create(['created_at' => '2026-02-28 23:30:00']);
        $after = $this->model::factory()
            ->create(['created_at' => '2026-03-01 00:30:00']);

        livewire(ListEmails::class)
            ->filterTable('headers-filter', [
                'from' => null,
                'to' => null,
                'cc' => null,
                'bcc' => null,
                'created_at' => '01/02/2026 - 28/02/2026',
                'attachments' => null,
            ])
            ->assertCanSeeTableRecords([$first, $last])
            ->assertCanNotSeeTableRecords([$before, $after]);
    } finally {
        date_default_timezone_set($default);
    }
})->with(['UTC', 'Europe/Athens', 'America/New_York']);

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
