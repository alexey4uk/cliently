<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Business>
 */
class BusinessFactory extends Factory
{
    protected $model = Business::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'name' => fake()->company(),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->text(200),
            'telegram_token' => fake()->uuid(),
            'telegram_chat_id' => null,
        ];
    }

    /**
     * Business с подключенным Telegram
     */
    public function withTelegram(): static
    {
        return $this->state(
            fn (array $attributes) => [
                'telegram_chat_id' => fake()->randomNumber(9, true),
            ],
        );
    }

    /**
     * Business с конкретным slug
     */
    public function withSlug(string $slug): static
    {
        return $this->state(['slug' => $slug]);
    }

    /**
     * Business с конкретным токеном
     */
    public function withToken(string $token): static
    {
        return $this->state(['telegram_token' => $token]);
    }
}
