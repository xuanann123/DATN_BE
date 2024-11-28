<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bill>
 */
class BillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_user' => rand(1, 20),
            'id_course' => rand(1, 20),
            'total_coin' => rand(100, 1000),
            'total_coin_after_discount' => rand(70, 1000),
            'status' => 'Thanh toán thành công',
            'created_at' => Carbon::now()->setMonth(rand(1, 12))->setDay(rand(1, 30)),
            'updated_at' => Carbon::now()->setMonth(rand(1, 12))->setDay(rand(1, 30)),
        ];
    }
}
