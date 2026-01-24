<?php

namespace Database\Seeders;

use App\Models\Business;
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
        // Получаем пользователя, созданного в UserSeeder
        $user = User::where('email', 'a@a.ru')->first();

        if (! $user) {
            $this->command->error('Пользователь с email a@a.ru не найден. Сначала запустите UserSeeder.');

            return;
        }

        // Проверяем, есть ли у пользователя уже бизнес
        if ($user->businesses()->exists()) {
            $this->command->info('У пользователя уже есть бизнес. Пропускаем создание.');

            return;
        }

        // Создаем бизнес
        $business = Business::create([
            'name' => 'Elite Beauty',
            'slug' => 'elite-beauty',
            'description' => 'Премиальный салон красоты с многолетним опытом работы. Мы предлагаем полный спектр услуг по уходу за волосами, ногтями и кожей.',
            'phone' => '+375291234567',
        ]);

        // Привязываем пользователя к бизнесу как владельца
        $ownerRole = \App\Models\BusinessRole::where('slug', 'owner')->first();
        if (!$ownerRole) {
            $this->command->error('Роль owner не найдена. Сначала запустите DefaultBusinessRolePermissionsSeeder.');
            return;
        }
        $business->users()->attach($user, ['role_id' => $ownerRole->id, 'role' => 'owner']);

        // Создаем локацию с рабочими часами
        $workingHours = [
            'monday' => ['from' => '09:00', 'to' => '21:00', 'day_off' => false],
            'tuesday' => ['from' => '09:00', 'to' => '21:00', 'day_off' => false],
            'wednesday' => ['from' => '09:00', 'to' => '21:00', 'day_off' => false],
            'thursday' => ['from' => '09:00', 'to' => '21:00', 'day_off' => false],
            'friday' => ['from' => '09:00', 'to' => '21:00', 'day_off' => false],
            'saturday' => ['from' => '10:00', 'to' => '20:00', 'day_off' => false],
            'sunday' => ['from' => null, 'to' => null, 'day_off' => true],
        ];

        $location = Location::create([
            'business_id' => $business->id,
            'name' => 'Главный салон',
            'city' => 'Минск',
            'street' => 'Независимости',
            'house' => '50',
            'apartment' => '201',
            'description' => 'Наш главный салон расположен в центре города. Удобная парковка и доступность общественным транспортом.',
            'phone' => '+375291234567',
            'working_hours' => json_encode($workingHours, JSON_UNESCAPED_UNICODE),
        ]);

        // Создаем услугу
        $service = Service::create([
            'business_id' => $business->id,
            'name' => 'Стрижка и укладка',
            'description' => 'Профессиональная стрижка волос с укладкой. Консультация стилиста по выбору прически.',
            'duration' => 60, // минут
            'price' => 45.00, // BYN
            'is_active' => true,
        ]);

        // Создаем мастера
        $master = Master::create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'first_name' => 'Анна',
            'last_name' => 'Петрова',
            'specialization' => 'Мастер-стилист',
            'description' => 'Опытный мастер-стилист с 10-летним стажем. Специализация: стрижки, окрашивание, укладки.',
            'phone' => '+375291111111',
            'email' => 'anna@elitebeauty.by',
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
