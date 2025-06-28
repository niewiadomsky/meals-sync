<?php

namespace Database\Factories;

use App\Models\Meal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $meals = cache()->remember('meals', 60, function () {
            return Meal::all();
        });
        
        $meal = $meals->random() ?? 0;

        return [
            'meal_id' => $meal->id,
            'user_id' => User::factory(),
            'content' => $this->faker->sentence,
        ];
    }
}
