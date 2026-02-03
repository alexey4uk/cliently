<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Country;
use App\Models\Plan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Создание тестового пользователя и подписки по умолчанию.
     */
    public function run(): void
    {
        // Client::factory(30)->create();
    }
}
