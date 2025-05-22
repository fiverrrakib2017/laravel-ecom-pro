<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_account_id' => null,
            'name' => $this->faker->unique()->company . ' Account',
            'type' => $this->faker->randomElement(['Asset', 'Liability', 'Income', 'Expense']),
            'description' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),

        ];
    }
}
