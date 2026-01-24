<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Plan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Используем firstOrCreate для безопасного создания (не создаст дубликат)
        $user = User::firstOrCreate(
            ['email' => 'a@a.ru'],
            [
                'name' => 'Иван',
                'phone' => '+375292909641',
                'password' => Hash::make('lm57iqxz'),
                'email_verified_at' => now(),
            ]
        );

        // Назначаем роль админа
        $user->assignRole('admin');

        // Автоматически создаем подписку на тариф по умолчанию, если её еще нет
        if (!$user->subscription()->exists()) {
            $defaultPlan = Plan::where('is_default', true)->first();

            // Если тариф по умолчанию не найден, пытаемся найти бесплатный тариф
            if (!$defaultPlan) {
                $defaultPlan = Plan::where('slug', 'free')->where('is_active', true)->first();
            }

            if ($defaultPlan) {
                $subscriptionService = app(SubscriptionService::class);
                $subscriptionService->createSubscription($user, $defaultPlan);
            }
        }

        // $user->businesses()->create([
        //     'name' => 'ИП Иванов',
        //     'slug' => fake()->slug(),
        //     'short_description' => 'Парикмахер'
        // ]);

        // Client::factory(30)->create();
    }
}
