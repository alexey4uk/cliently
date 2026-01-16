<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\TelegramUserState;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TelegramUserState>
 */
class TelegramUserStateFactory extends Factory
{
    protected $model = TelegramUserState::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'telegram_user_id' => fake()->randomNumber(9, true),
            'step' => TelegramUserState::STEP_START,
            'data' => [],
            'business_id' => null,
            'last_message_id' => null,
        ];
    }

    /**
     * State для поиска
     */
    public function search(): static
    {
        return $this->state([
            'step' => TelegramUserState::STEP_SEARCH,
            'business_id' => null,
            'data' => ['search_query' => fake()->word()],
        ]);
    }

    /**
     * State для процесса записи
     */
    public function booking(Business $business, string $step, array $data = []): static
    {
        return $this->state([
            'step' => $step,
            'business_id' => $business->id,
            'data' => $data,
        ]);
    }

    /**
     * State с last_message_id
     */
    public function withMessageId(int $messageId): static
    {
        return $this->state(['last_message_id' => $messageId]);
    }

    /**
     * State для конкретного пользователя
     */
    public function forUser(string $telegramUserId): static
    {
        return $this->state(['telegram_user_id' => $telegramUserId]);
    }
}
