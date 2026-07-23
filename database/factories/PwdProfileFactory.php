<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PwdProfile>
 */
class PwdProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'oku_number' => 'OKU' . $this->faker->randomNumber(8, true),
            'skills' => $this->faker->randomElements(['Data Entry', 'Customer Service', 'Graphic Design', 'Accounting', 'Programming'], 2),
            'physical_needs' => $this->faker->randomElements(['Wheelchair Ramp', 'Elevator Access', 'Accessible Restroom'], 1),
            'tech_needs' => $this->faker->randomElements(['Screen Reader Software', 'Adjustable Workstation'], 1),
            'work_arrangement' => $this->faker->randomElements(['Flexible Working Hours', 'Remote Work', 'Hybrid Work'], 1),
            'sensory_needs' => $this->faker->randomElements(['Sign Language Interpreter', 'Written Communication'], 1),
            'status' => 'approved',
        ];
    }
}
