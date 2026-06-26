<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobListing>
 */
class JobListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle,
            'description' => fake()->paragraph(3),
            'category' => fake()->randomElement(['it', 'design', 'marketing', 'data', 'management']),
            'industry' => fake()->randomElement(['software', 'creative', 'retail', 'fintech', 'finance']),
            'location' => fake()->randomElement(['cairo', 'giza', 'alexandria']),
            'company' => fake()->company,
            'status' => 'published',
            'deadline' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'salary_min' => fake()->numberBetween(5000, 15000),
            'salary_max' => fake()->numberBetween(15000, 30000),
            'experience_level' => fake()->randomElement(['junior', 'mid', 'senior']),
        ];
    }
}
