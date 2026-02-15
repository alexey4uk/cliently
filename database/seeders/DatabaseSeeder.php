<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Заполнение базы данных приложения.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            RolePermissionSeeder::class,
            DefaultBusinessRolePermissionsSeeder::class,
            SubscriptionMetricSeeder::class,
            PlanSeeder::class,
        ]);
    }
}
