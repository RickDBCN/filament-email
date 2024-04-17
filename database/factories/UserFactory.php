<?php

namespace MG87\FilamentEmail\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MG87\FilamentEmail\Tests\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            //'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
        ];
    }
}
