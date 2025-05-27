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
    public function definition(): array
    {
        $makes = ['Toyota', 'Honda', 'Ford', 'Chevrolet', 'BMW', 'Mercedes', 'Audi', 'Volkswagen', 'Nissan', 'Hyundai'];
        $models = ['Model S', 'Civic', 'F-150', 'Malibu', '3 Series', 'C-Class', 'A4', 'Golf', 'Altima', 'Elantra'];

        return [
            'make' => $this->faker->randomElement($makes),
            'model' => $this->faker->randomElement($models),
            'year' => $this->faker->numberBetween(2000, 2025),
            'price' => $this->faker->numberBetween(15000, 100000),
        ];
    }
}
