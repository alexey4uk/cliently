<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Plan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //        User::factory()->create([
        //            'name' => 'Test User',
        //            'email' => 'test@example.com',
        //        ]);

        $this->call([
            CountrySeeder::class, // Справочник стран (до phones, форм и т.д.)
            RolePermissionSeeder::class,
            DefaultBusinessRolePermissionsSeeder::class,
            SubscriptionMetricSeeder::class, // Метрики подписки должны быть созданы до тарифов
            PlanSeeder::class, // Тарифы используют метрики
            // Админ создаётся через /setup при первом запуске, не через сидер.
            // UserSeeder::class,
            // OnboardingSeeder::class, // зависит от UserSeeder (a@a.ru)
        ]);

        // Создание тестового админа
        $admin = User::firstOrCreate(
            ['email' => 'a@a.ru'],
            [
                'name' => 'Администратор',
                'password' => Hash::make('lm57iqxz'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('admin');

        // Автоматически создаем подписку на тариф по умолчанию, если её еще нет
        if (! $admin->subscription()->exists()) {
            $defaultPlan = Plan::where('is_default', true)->first();

            // Если тариф по умолчанию не найден, пытаемся найти бесплатный тариф
            if (! $defaultPlan) {
                $defaultPlan = Plan::where('slug', 'free')->where('is_active', true)->first();
            }

            if ($defaultPlan) {
                $subscriptionService = app(SubscriptionService::class);
                $subscriptionService->createSubscription($admin, $defaultPlan);
            }
        }

        // Business::factory(20)->create();
    }
}
