<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployerProfile>
 */
class EmployerProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => $this->faker->company(),
            'company_address' => $this->faker->address(),
            'industry_type' => $this->faker->randomElement(['Technology', 'Finance', 'Healthcare', 'Education', 'Retail']),
            'ssm_number' => $this->faker->randomNumber(8, true) . '-X',
            'contact_number' => $this->faker->phoneNumber(),
            'company_email' => $this->faker->companyEmail(),
            'accessibility' => $this->faker->randomElements(['Wheelchair Ramp', 'Elevator Access', 'Accessible Restroom', 'Screen Reader Software'], 2),
            'status' => 'approved',
        ];
    }
}
