<?php

namespace Database\Factories;

use App\Enums\TransactionTypeEnum;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wallet_id' => Wallet::factory()->create()->id,
            'type'      => TransactionTypeEnum::DEPOSIT->value,
            'amount'    => $this->faker->randomFloat(2, 1, 100),
            'final_balance' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}
