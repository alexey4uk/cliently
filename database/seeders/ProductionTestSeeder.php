<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Client;
use App\Models\Location;
use App\Models\Master;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProductionTestSeeder extends Seeder
{
    private int $usersCount;

    private int $clientsPerBusiness;

    private int $servicesPerBusiness;

    private int $mastersPerBusiness;

    private int $appointmentsPerBusiness;

    private array $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];

    private array $sources = ['web', 'telegram', 'manual'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Читаем из переменных окружения или используем значения по умолчанию
        $this->usersCount = (int) (getenv('SEED_USERS_COUNT') ?: 100);
        $this->clientsPerBusiness = (int) (getenv('SEED_CLIENTS_PER_BUSINESS') ?: 50);
        $this->servicesPerBusiness = (int) (getenv('SEED_SERVICES_PER_BUSINESS') ?: 8);
        $this->mastersPerBusiness = (int) (getenv('SEED_MASTERS_PER_BUSINESS') ?: 3);
        $this->appointmentsPerBusiness = (int) (getenv('SEED_APPOINTMENTS_PER_BUSINESS') ?: 100);

        $this->command->info('🚀 Запуск ProductionTestSeeder...');
        $startTime = microtime(true);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->command->info('📝 Создание пользователей и бизнесов...');
        $users = $this->createUsers();

        $this->command->info('🏢 Создание бизнесов и привязка пользователей...');
        $businesses = $this->createBusinessesWithUsers($users);

        $this->command->info('📍 Создание локаций...');
        $locations = $this->createLocations($businesses);

        $this->command->info('💼 Создание услуг...');
        $services = $this->createServices($businesses);

        $this->command->info('👨‍💼 Создание мастеров...');
        $masters = $this->createMasters($businesses, $users);

        $this->command->info('👥 Создание клиентов...');
        $clients = $this->createClients($businesses);

        $this->command->info('📅 Создание записей...');
        $this->createAppointments($businesses, $clients, $services, $masters, $locations);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $duration = round(microtime(true) - $startTime, 2);
        $this->command->info("✅ Готово! Время выполнения: {$duration} сек");
        $this->printStatistics();
    }

    private function createUsers(): array
    {
        $users = [];
        $now = now();

        for ($i = 1; $i <= $this->usersCount; $i++) {
            $users[] = [
                'name' => fake('ru_RU')->name(),
                'email' => "user{$i}@cliently.test",
                'email_verified_at' => $now,
                'password' => Hash::make('password'),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('users')->insert($users);

        // Назначаем роль 'user' всем пользователям
        $allUsers = User::all();
        foreach ($allUsers as $user) {
            $user->assignRole('user');
        }

        $this->command->info('   ✓ Создано пользователей: '.count($users));

        return $allUsers->keyBy('id')->all();
    }

    private function createBusinessesWithUsers(array $users): array
    {
        $businesses = [];
        $businessUserPivots = [];
        $now = now();
        $businessId = 1;

        foreach ($users as $userId => $user) {
            $businessName = fake('ru_RU')->company();
            $businesses[] = [
                'id' => $businessId,
                'name' => $businessName,
                'slug' => 'business-'.$businessId,
                'description' => fake('ru_RU')->sentence(10),
                'online_booking_enabled' => fake()->boolean(80),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Привязываем владельца к бизнесу
            $businessUserPivots[] = [
                'business_id' => $businessId,
                'user_id' => $userId,
                'role' => 'owner',
                'first_name' => fake('ru_RU')->firstName(),
                'last_name' => fake('ru_RU')->lastName(),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $businessId++;
        }

        DB::table('businesses')->insert($businesses);
        DB::table('business_user')->insert($businessUserPivots);

        $this->command->info('   ✓ Создано бизнесов: '.count($businesses));

        return Business::all()->keyBy('id')->all();
    }

    private function createLocations(array $businesses): array
    {
        $locations = [];
        $now = now();

        foreach ($businesses as $businessId => $business) {
            $workingHours = [
                'from' => '09:00',
                'to' => '18:00',
                '24_hours' => false,
                'days_off' => [0, 6], // Выходные: воскресенье и суббота
            ];

            $locations[] = [
                'business_id' => $businessId,
                'name' => 'Главный офис',
                'city' => fake('ru_RU')->city(),
                'street' => fake('ru_RU')->streetName(),
                'house' => fake('ru_RU')->buildingNumber(),
                'working_hours' => json_encode($workingHours),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('locations')->insert($locations);

        $this->command->info('   ✓ Создано локаций: '.count($locations));

        return Location::all()->keyBy('business_id')->all();
    }

    private function createServices(array $businesses): array
    {
        $services = [];
        $now = now();
        $serviceTypes = [
            ['name' => 'Стрижка', 'duration' => 30, 'price' => 500],
            ['name' => 'Укладка', 'duration' => 45, 'price' => 800],
            ['name' => 'Окрашивание', 'duration' => 120, 'price' => 2500],
            ['name' => 'Маникюр', 'duration' => 60, 'price' => 1000],
            ['name' => 'Педикюр', 'duration' => 90, 'price' => 1500],
            ['name' => 'Массаж', 'duration' => 60, 'price' => 2000],
            ['name' => 'Консультация', 'duration' => 30, 'price' => 500],
            ['name' => 'Комплексная процедура', 'duration' => 180, 'price' => 5000],
        ];

        foreach ($businesses as $businessId => $business) {
            foreach (array_slice($serviceTypes, 0, $this->servicesPerBusiness) as $serviceType) {
                $services[] = [
                    'business_id' => $businessId,
                    'name' => $serviceType['name'],
                    'description' => fake('ru_RU')->sentence(5),
                    'duration' => $serviceType['duration'],
                    'price' => $serviceType['price'] + fake()->numberBetween(-100, 500),
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Вставляем порциями
        $chunks = array_chunk($services, 500);
        foreach ($chunks as $chunk) {
            DB::table('services')->insert($chunk);
        }

        $this->command->info('   ✓ Создано услуг: '.count($services));

        return Service::all()->groupBy('business_id')->all();
    }

    private function createMasters(array $businesses, array $users): array
    {
        $masters = [];
        $serviceMasterPivots = [];
        $masterLocationPivots = [];
        $now = now();

        $workingHours = [
            'from' => '09:00',
            'to' => '18:00',
            '24_hours' => false,
            'days_off' => [0, 6], // Выходные
        ];

        $specializations = [
            'Парикмахер', 'Стилист', 'Мастер маникюра',
            'Массажист', 'Косметолог', 'Барбер',
        ];

        foreach ($businesses as $businessId => $business) {
            $businessServices = Service::where('business_id', $businessId)->pluck('id')->toArray();
            $businessLocation = Location::where('business_id', $businessId)->first();

            for ($i = 0; $i < $this->mastersPerBusiness; $i++) {
                $masterId = DB::table('masters')->insertGetId([
                    'business_id' => $businessId,
                    'user_id' => null, // Не привязываем к пользователям для простоты
                    'first_name' => fake('ru_RU')->firstName(),
                    'last_name' => fake('ru_RU')->lastName(),
                    'specialization' => fake()->randomElement($specializations),
                    'description' => fake('ru_RU')->sentence(8),
                    'email' => fake()->optional(0.5)->safeEmail(),
                    'working_hours' => json_encode($workingHours),
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Привязываем мастера к услугам (2-5 услуг на мастера)
                $masterServicesCount = min(fake()->numberBetween(2, 5), count($businessServices));
                $masterServices = fake()->randomElements($businessServices, $masterServicesCount);

                foreach ($masterServices as $serviceId) {
                    $serviceMasterPivots[] = [
                        'service_id' => $serviceId,
                        'master_id' => $masterId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Привязываем мастера к локации
                if ($businessLocation) {
                    $masterLocationPivots[] = [
                        'master_id' => $masterId,
                        'location_id' => $businessLocation->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $masters[] = $masterId;
            }
        }

        // Вставляем связи порциями, чтобы не превысить лимит placeholders MySQL
        $chunks = array_chunk($serviceMasterPivots, 1000);
        foreach ($chunks as $chunk) {
            DB::table('service_master')->insert($chunk);
        }

        $chunks = array_chunk($masterLocationPivots, 1000);
        foreach ($chunks as $chunk) {
            DB::table('master_location')->insert($chunk);
        }

        $this->command->info('   ✓ Создано мастеров: '.count($masters));

        return Master::all()->groupBy('business_id')->all();
    }

    private function createClients(array $businesses): array
    {
        $clients = [];
        $now = now();

        foreach ($businesses as $businessId => $business) {
            for ($i = 0; $i < $this->clientsPerBusiness; $i++) {
                $clients[] = [
                    'business_id' => $businessId,
                    'first_name' => fake('ru_RU')->firstName(),
                    'last_name' => fake('ru_RU')->lastName(),
                    'email' => fake()->optional(0.7)->safeEmail(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Вставляем порциями
        $chunks = array_chunk($clients, 1000);
        foreach ($chunks as $chunk) {
            DB::table('clients')->insert($chunk);
        }

        $this->command->info('   ✓ Создано клиентов: '.count($clients));

        return Client::all()->groupBy('business_id')->all();
    }

    private function createAppointments(array $businesses, array $clients, array $services, array $masters, array $locations): void
    {
        $appointments = [];
        $now = now();
        $appointmentCount = 0;

        foreach ($businesses as $businessId => $business) {
            $businessClients = $clients[$businessId] ?? [];
            $businessServices = $services[$businessId] ?? [];
            $businessMasters = $masters[$businessId] ?? [];
            $businessLocation = $locations[$businessId] ?? null;

            if (empty($businessClients) || empty($businessServices) || empty($businessMasters) || ! $businessLocation) {
                continue;
            }

            // Создаем записи: 30% прошлые, 70% будущие
            $pastCount = (int) ($this->appointmentsPerBusiness * 0.3);
            $futureCount = $this->appointmentsPerBusiness - $pastCount;

            // Прошлые записи (последние 60 дней)
            for ($i = 0; $i < $pastCount; $i++) {
                $date = Carbon::today()->subDays(fake()->numberBetween(1, 60));
                $appointment = $this->generateAppointment(
                    $businessId,
                    $businessClients,
                    $businessServices,
                    $businessMasters,
                    $businessLocation,
                    $date,
                    true // прошлая запись
                );
                $appointments[] = $appointment;
                $appointmentCount++;
            }

            // Будущие записи (следующие 90 дней)
            for ($i = 0; $i < $futureCount; $i++) {
                $date = Carbon::today()->addDays(fake()->numberBetween(1, 90));
                $appointment = $this->generateAppointment(
                    $businessId,
                    $businessClients,
                    $businessServices,
                    $businessMasters,
                    $businessLocation,
                    $date,
                    false // будущая запись
                );
                $appointments[] = $appointment;
                $appointmentCount++;
            }

            // Вставляем порциями после каждого бизнеса для экономии памяти
            if (count($appointments) >= 1000) {
                DB::table('appointments')->insert($appointments);
                $appointments = [];
            }
        }

        // Вставляем оставшиеся
        if (! empty($appointments)) {
            DB::table('appointments')->insert($appointments);
        }

        $this->command->info("   ✓ Создано записей: {$appointmentCount}");
    }

    private function generateAppointment(
        int $businessId,
        $clients,
        $services,
        $masters,
        $location,
        Carbon $date,
        bool $isPast
    ): array {
        $client = fake()->randomElement($clients);
        $service = fake()->randomElement($services);

        // Просто выбираем случайного мастера из доступных
        // (связь мастер-услуга уже создана при создании мастеров)
        $master = fake()->randomElement($masters);

        // Генерируем время в рабочие часы (9:00 - 18:00)
        $hour = fake()->numberBetween(9, 17);
        $minute = fake()->randomElement([0, 15, 30, 45]);
        $time = sprintf('%02d:%02d', $hour, $minute);

        // Для прошлых записей больше завершенных, для будущих - больше подтвержденных
        if ($isPast) {
            $status = fake()->randomElement([
                'completed', 'completed', 'completed', // 60% завершены
                'cancelled', // 20% отменены
                'pending', // 20% ожидают
            ]);
        } else {
            $status = fake()->randomElement([
                'confirmed', 'confirmed', 'confirmed', // 60% подтверждены
                'pending', 'pending', // 40% ожидают
            ]);
        }

        $source = fake()->randomElement($this->sources);

        return [
            'business_id' => $businessId,
            'client_id' => $client->id,
            'service_id' => $service->id,
            'master_id' => $master->id,
            'location_id' => $location->id,
            'date' => $date->format('Y-m-d'),
            'time' => $time,
            'status' => $status,
            'source' => $source,
            'notes' => fake()->optional(0.3)->sentence(10),
            'duration' => $service->duration,
            'price' => $service->price,
            'token' => $this->generateAppointmentToken(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Генерация уникального токена для записи
     * Формат: abc-123-def-456
     */
    private function generateAppointmentToken(): string
    {
        $letters1 = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 3);
        $digits1 = substr(str_shuffle('0123456789'), 0, 3);
        $letters2 = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 3);
        $digits2 = substr(str_shuffle('0123456789'), 0, 3);

        return strtolower($letters1.'-'.$digits1.'-'.$letters2.'-'.$digits2);
    }

    private function printStatistics(): void
    {
        $stats = [
            'Пользователей' => User::count(),
            'Бизнесов' => Business::count(),
            'Локаций' => Location::count(),
            'Услуг' => Service::count(),
            'Мастеров' => Master::count(),
            'Клиентов' => Client::count(),
            'Записей' => Appointment::count(),
        ];

        $this->command->info("\n📊 Статистика:");
        foreach ($stats as $name => $count) {
            $this->command->info("   {$name}: {$count}");
        }

        $this->command->info("\n💡 Для входа используйте:");
        $this->command->info('   Email: user1@cliently.test');
        $this->command->info('   Password: password');
    }
}
