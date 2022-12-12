<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        static $owner_id = 1;
        return [
            'name' => fake()->unique()->word(),
            'price' =>  (int) rand(30000, 100000),
            'owner_id' => $owner_id++,
        ];
    }
}
