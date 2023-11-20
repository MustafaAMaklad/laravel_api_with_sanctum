<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Client;
use App\Models\City;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
           'first_name' => fake()->firstName,
           'last_name' => fake()->lastName,
        ];
    }
     /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Client $client) {
            $client_id = $client->id;
            Cart::factory()->createOne(['client_id' => $client_id]);
            Wishlist::factory()->createOne(['client_id' => $client_id]);
        });
    }
}
