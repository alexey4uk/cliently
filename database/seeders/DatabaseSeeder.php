<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        // Business::factory(20)->create();
    }
}
