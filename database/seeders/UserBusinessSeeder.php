<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Location;
use App\Models\Master;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserBusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем пользователя
        $user = User::firstOrCreate(
            ["email" => "test@example.com"],
            [
                "name" => "Тестовый Пользователь",
                "password" => Hash::make("password"),
                "email_verified_at" => now(),
            ],
        );

        $this->command->info("Пользователь создан: {$user->email}");

        // Создаем бизнес
        $business = Business::create([
            "name" => "Тестовый Бизнес",
            "slug" => "test-business",
            "description" => "Описание тестового бизнеса",
            "online_booking_enabled" => true,
        ]);

        $this->command->info("Бизнес создан: {$business->name}");

        // Привязываем пользователя к бизнесу
        $ownerRole = \App\Models\BusinessRole::where("slug", "owner")->first();
        if ($ownerRole) {
            $business->users()->attach($user, [
                "role_id" => $ownerRole->id,
                "first_name" => "Тестовый",
                "last_name" => "Пользователь",
            ]);
        } else {
            $business->users()->attach($user, [
                "first_name" => "Тестовый",
                "last_name" => "Пользователь",
            ]);
        }

        // Создаем локацию
        $workingHours = [
            "monday" => [
                "from" => "09:00",
                "to" => "21:00",
                "day_off" => false,
            ],
            "tuesday" => [
                "from" => "09:00",
                "to" => "21:00",
                "day_off" => false,
            ],
            "wednesday" => [
                "from" => "09:00",
                "to" => "21:00",
                "day_off" => false,
            ],
            "thursday" => [
                "from" => "09:00",
                "to" => "21:00",
                "day_off" => false,
            ],
            "friday" => [
                "from" => "09:00",
                "to" => "21:00",
                "day_off" => false,
            ],
            "saturday" => [
                "from" => "10:00",
                "to" => "20:00",
                "day_off" => false,
            ],
            "sunday" => ["from" => null, "to" => null, "day_off" => true],
        ];

        $location = Location::create([
            "business_id" => $business->id,
            "name" => "Главная локация",
            "city" => "Минск",
            "street" => "Тестовая",
            "house" => "1",
            "description" => "Описание локации",
            "working_hours" => json_encode(
                $workingHours,
                JSON_UNESCAPED_UNICODE,
            ),
        ]);

        $this->command->info("Локация создана: {$location->name}");

        // Создаем услугу
        $service = Service::create([
            "business_id" => $business->id,
            "name" => "Тестовая услуга",
            "description" => "Описание тестовой услуги",
            "duration" => 60,
            "price" => 50.0,
            "is_active" => true,
        ]);

        $this->command->info("Услуга создана: {$service->name}");

        // Создаем мастера
        $master = Master::create([
            "business_id" => $business->id,
            "user_id" => $user->id,
            "first_name" => "Иван",
            "last_name" => "Иванов",
            "specialization" => "Мастер",
            "description" => "Описание мастера",
            "email" => "master@example.com",
            "is_active" => true,
        ]);

        $this->command->info(
            "Мастер создан: {$master->first_name} {$master->last_name}",
        );

        // Привязываем мастера к локации
        $master->locations()->attach($location->id);

        // Привязываем мастера к услуге
        $master->services()->attach($service->id, ["price" => 50.0]);

        $this->command->info("Все данные успешно созданы!");
        $this->command->info("Пользователь: {$user->email} (пароль: password)");
        $this->command->info(
            "Бизнес: {$business->name} (slug: {$business->slug})",
        );
        $this->command->info("Локация: {$location->name}");
        $this->command->info("Услуга: {$service->name}");
        $this->command->info(
            "Мастер: {$master->first_name} {$master->last_name}",
        );
    }
}
