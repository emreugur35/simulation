database/factories/SeasonFactory.php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Season>
 */
class SeasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Season ' . $this->faker->year(),
            'weeks' => $this->faker->numberBetween(30, 38),
            'current_week' => 1,
            'completed' => false,
            'winner' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the season with a specific number of weeks.
     *
     * @param int $weeks
     * @return $this
     */
    public function withWeeks(int $weeks)
    {
        return $this->state(function (array $attributes) use ($weeks) {
            return [
                'weeks' => $weeks,
            ];
        });
    }

    /**
     * Configure the season with a specific current week.
     *
     * @param int $currentWeek
     * @return $this
     */
    public function atWeek(int $currentWeek)
    {
        return $this->state(function (array $attributes) use ($currentWeek) {
            return [
                'current_week' => $currentWeek,
            ];
        });
    }

    /**
     * Configure the season as completed.
     *
     * @param int|null $winnerId
     * @return $this
     */
    public function completed(int $winnerId = null)
    {
        return $this->state(function (array $attributes) use ($winnerId) {
            return [
                'completed' => true,
                'winner' => $winnerId,
                'current_week' => $attributes['weeks'],
            ];
        });
    }
}