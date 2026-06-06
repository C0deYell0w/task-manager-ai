<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'priority' => fake()->randomElement(array_column(\App\Enums\PriorityEnum::cases(), 'value')),
            'status' => fake()->randomElement(array_column(\App\Enums\StatusEnum::cases(), 'value')),
            'due_date' => fake()->boolean(80) ? fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d') : null,
            'assigned_to' => \App\Models\User::inRandomOrder()->first()?->id ?? \App\Models\User::factory(),
            'ai_summary' => null,
            'ai_priority' => null,
        ];
    }
}
