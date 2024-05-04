<?php

namespace RickDBCN\FilamentEmail\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RickDBCN\FilamentEmail\FilamentEmailServiceProvider;
use RickDBCN\FilamentEmail\Providers\EmailMessageServiceProvider;
use RickDBCN\FilamentEmail\Tests\Models\User;
use RickDBCN\FilamentEmail\Tests\Panels\TestPanelProvider;

class TestCase extends Orchestra
{
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'RickDBCN\\FilamentEmail\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        $packageProviders = [
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            LivewireServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            ActionsServiceProvider::class,
            WidgetsServiceProvider::class,

            EmailMessageServiceProvider::class,
            FilamentEmailServiceProvider::class,

            TestPanelProvider::class,
        ];

        if (class_exists(NotificationsServiceProvider::class)) {
            $packageProviders[] = NotificationsServiceProvider::class;
        }

        return $packageProviders;
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/../database/migrations/create_filament_email_table.php.stub';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/add_attachments_field_to_filament_email_log_table.php.stub';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/add_team_id_field_to_filament_email_log_table.php.stub';
        $migration->up();
    }

    /**
     * Set up the database.
     *
     * @param  Application  $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('name');
        });

        $this->adminUser = User::create(['email' => 'admin@domain.com', 'name' => 'Admin']);

        //self::$migration->up();
    }
}
