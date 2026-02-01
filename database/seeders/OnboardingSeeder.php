<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Country;
use App\Models\Location;
use App\Models\Master;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class OnboardingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where("email", "a@a.ru")->first();

        if (!$user) {
            $this->command->error(
                "Пользователь с email a@a.ru не найден. Сначала запустите UserSeeder.",
            );

            return;
        }

        if ($user->businesses()->exists()) {
            $this->command->info(
                "У пользователя уже есть бизнес. Пропускаем создание.",
            );

            return;
        }

        $countryBy = Country::where("code", "BY")->first();
        if (!$countryBy) {
            $this->command->error(
                "Страна BY не найдена. Сначала запустите CountrySeeder.",
            );

            return;
        }

        $business = Business::create([
            "name" => "Elite Beauty",
            "slug" => "elite-beauty",
            "description" =>
                "Премиальный салон красоты с многолетним опытом работы. Мы предлагаем полный спектр услуг по уходу за волосами, ногтями и кожей.",
        ]);

        $business->phones()->create([
            "country_id" => $countryBy->id,
            "phone" => "+375291234567",
            "type" => "primary",
        ]);

        $ownerRole = \App\Models\BusinessRole::where("slug", "owner")->first();
        if (!$ownerRole) {
            $this->command->error(
                "Роль owner не найдена. Сначала запустите DefaultBusinessRolePermissionsSeeder.",
            );

            return;
        }
        $business->users()->attach($user, ["role_id" => $ownerRole->id]);

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
            "name" => "Главный салон",
            "city" => "Минск",
            "street" => "Независимости",
            "house" => "50",
            "apartment" => "201",
            "description" =>
                "Наш главный салон расположен в центре города. Удобная парковка и доступность общественным транспортом.",
            "working_hours" => json_encode(
                $workingHours,
                JSON_UNESCAPED_UNICODE,
            ),
        ]);

        $location->phones()->create([
            "country_id" => $countryBy->id,
            "phone" => "+375291234567",
            "type" => "primary",
        ]);

        $service = Service::create([
            "business_id" => $business->id,
            "name" => "Стрижка и укладка",
            "description" =>
                "Профессиональная стрижка волос с укладкой. Консультация стилиста по выбору прически.",
            "duration" => 60,
            "price" => 45.0,
            "is_active" => true,
        ]);

        $master = Master::create([
            "business_id" => $business->id,
            "user_id" => $user->id,
            "first_name" => "Анна",
            "last_name" => "Петрова",
            "specialization" => "Мастер-стилист",
            "description" =>
                "Опытный мастер-стилист с 10-летним стажем. Специализация: стрижки, окрашивание, укладки.",
            "email" => "anna@elitebeauty.by",
        ]);

        $master->phones()->create([
            "country_id" => $countryBy->id,
            "phone" => "+375291111111",
            "type" => "primary",
        ]);

        // Привязываем мастера к локации и услуге
        $master->locations()->attach($location->id);
        $master->services()->attach($service->id);

        // $this->command->info('Onboarding данные успешно созданы!');
        // $this->command->info("Пользователь: {$user->email} (пароль: lm57iqxz)");
        // $this->command->info("Бизнес: {$business->name} (slug: {$business->slug})");
        // $this->command->info("Локация: {$location->name}");
        // $this->command->info("Услуга: {$service->name}");
        // $this->command->info("Мастер: {$master->first_name} {$master->last_name}");
    }
}
