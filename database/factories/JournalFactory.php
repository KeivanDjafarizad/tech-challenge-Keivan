<?php

namespace Database\Factories;

use App\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class JournalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'notes' => $this->faker->paragraph,
            'entry_date' => $this->faker->dateTimeThisYear,
        ];
    }

    public function belongsToClient(Client $client)
    {
        return $this->state(function (array $attributes) use ($client) {
            return [
                'client_id' => $client->id,
            ];
        });
    }
}
