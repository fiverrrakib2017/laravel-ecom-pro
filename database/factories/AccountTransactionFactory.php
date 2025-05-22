<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account_transaction>
 */
class AccountTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $accountIds = \App\Models\Account::pluck('id')->toArray();
        $from = $this->faker->randomElement($accountIds);
        $to = $this->faker->randomElement(array_diff($accountIds, [$from]));
        return [
            'account_id' => $from,
            'related_account_id' => $to,
            'amount' => $this->faker->randomFloat(2, 100, 20000),
            'description' => $this->faker->sentence(),
            'transaction_date' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
