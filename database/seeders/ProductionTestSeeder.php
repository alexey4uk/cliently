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

class ProductionTestSeeder extends Seeder
{
    private int $usersCount;

    private int $clientsPerBusiness;

    private int $servicesPerBusiness;

    private int $mastersPerBusiness;

    private int $appointmentsPerBusiness;

    private array $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];

    private array $sources = ['web', 'telegram', 'manual'];

    private int $totalYearsBack;

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

        // Настраиваем временной диапазон для распределения данных
        $this->totalYearsBack = (int) (getenv('SEED_TOTAL_YEARS_BACK') ?: $this->calculateTotalYearsBack($this->usersCount * $this->appointmentsPerBusiness));

        $this->command->info('🚀 Запуск ProductionTestSeeder...');
        $this->command->info("📅 Временной диапазон: {$this->totalYearsBack} лет назад");
        $startTime = microtime(true);

        // Оптимизация MySQL для массовой вставки
        DB::statement('SET unique_checks=0');
        DB::statement('SET foreign_key_checks=0');

        try {
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

            // Восстанавливаем настройки MySQL
            DB::statement('SET foreign_key_checks=1');
            DB::statement('SET unique_checks=1');

            $duration = round(microtime(true) - $startTime, 2);
            $this->command->info("✅ Готово! Время выполнения: {$duration} сек");
            $this->printStatistics();
        } catch (\Exception $e) {
            // Восстанавливаем настройки MySQL даже при ошибке
            DB::statement('SET foreign_key_checks=1');
            DB::statement('SET unique_checks=1');
            $this->command->error('❌ Ошибка при выполнении сидера: '.$e->getMessage());
            throw $e;
        }
    }

    private function createUsers(): array
    {
        $users = [];

        // Статический хэш пароля для ускорения генерации
        $staticPasswordHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

        // Предварительно генерируем имена для ускорения
        $names = [];
        $faker = fake('ru_RU');
        for ($i = 0; $i < min(1000, $this->usersCount); $i++) {
            $names[] = $faker->name();
        }

        // Распределяем пользователей по времени
        $startTimestamp = Carbon::now()->subYears($this->totalYearsBack)->timestamp;
        $endTimestamp = Carbon::now()->timestamp;

        $progressBar = $this->command->getOutput()->createProgressBar($this->usersCount);
        $progressBar->start();

        for ($i = 1; $i <= $this->usersCount; $i++) {
            // Распределяем дату создания пользователя
            $userCreatedAt = $this->getDistributedTimestamp($startTimestamp, $endTimestamp, $i - 1, $this->usersCount, 'uniform');
            $userCreatedAtFormatted = date('Y-m-d H:i:s', $userCreatedAt);

            $users[] = [
                'name' => $names[$i % count($names)],
                'email' => "user{$i}@cliently.test",
                'email_verified_at' => $userCreatedAtFormatted,
                'password' => $staticPasswordHash,
                'created_at' => $userCreatedAtFormatted,
                'updated_at' => $userCreatedAtFormatted,
            ];

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine();

        // Оптимизируем размер пакета в зависимости от объема данных
        $chunkSize = $this->usersCount > 100000 ? 5000 : 1000;
        $chunks = array_chunk($users, $chunkSize);
        foreach ($chunks as $chunk) {
            DB::table('users')->insert($chunk);
        }

        $this->command->info('   ✓ Создано пользователей: '.count($users));

        // Возвращаем данные из массива вместо повторного чтения из БД
        $result = [];
        foreach ($users as $i => $user) {
            $result[$i + 1] = (object) array_merge(['id' => $i + 1], $user);
        }

        return $result;
    }

    private function createBusinessesWithUsers(array $users): array
    {
        $businesses = [];
        $businessUserPivots = [];
        $businessId = 1;

        $progressBar = $this->command->getOutput()->createProgressBar(count($users));
        $progressBar->start();

        // Распределяем создание бизнесов по временному диапазону
        $startTimestamp = Carbon::now()->subYears($this->totalYearsBack)->timestamp;
        $endTimestamp = Carbon::now()->timestamp;

        foreach ($users as $userId => $user) {
            // Распределяем бизнесы равномерно по временному диапазону
            $businessCreatedAt = $this->getDistributedTimestamp($startTimestamp, $endTimestamp, $businessId - 1, $this->usersCount, 'uniform');
            $businessCreatedAtFormatted = date('Y-m-d H:i:s', $businessCreatedAt);

            $businessName = fake('ru_RU')->company();
            $businesses[] = [
                'id' => $businessId,
                'name' => $businessName,
                'slug' => 'business-'.$businessId,
                'description' => fake('ru_RU')->sentence(10),
                'online_booking_enabled' => fake()->boolean(80),
                'created_at' => $businessCreatedAtFormatted,
                'updated_at' => $businessCreatedAtFormatted,
            ];

            // Привязываем владельца к бизнесу
            $businessUserPivots[] = [
                'business_id' => $businessId,
                'user_id' => $userId,
                'role' => 'owner',
                'first_name' => fake('ru_RU')->firstName(),
                'last_name' => fake('ru_RU')->lastName(),
                'created_at' => $businessCreatedAtFormatted,
                'updated_at' => $businessCreatedAtFormatted,
            ];

            $businessId++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine();

        // Используем транзакцию для вставки бизнесов
        DB::beginTransaction();
        try {
            // Оптимизируем размер пакета в зависимости от объема данных
            $chunkSize = count($businesses) > 100000 ? 5000 : 1000;
            $businessChunks = array_chunk($businesses, $chunkSize);
            foreach ($businessChunks as $chunk) {
                DB::table('businesses')->insert($chunk);
            }

            $pivotChunks = array_chunk($businessUserPivots, $chunkSize);
            foreach ($pivotChunks as $chunk) {
                DB::table('business_user')->insert($chunk);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $this->command->info('   ✓ Создано бизнесов: '.count($businesses));

        // Возвращаем данные из массива вместо повторного чтения из БД
        $result = [];
        foreach ($businesses as $business) {
            $result[$business['id']] = (object) $business;
        }

        return $result;
    }

    private function createLocations(array $businesses): array
    {
        $locations = [];

        $progressBar = $this->command->getOutput()->createProgressBar(count($businesses));
        $progressBar->start();

        foreach ($businesses as $businessId => $business) {
            $workingHours = [
                'from' => '09:00',
                'to' => '18:00',
                '24_hours' => false,
                'days_off' => [0, 6], // Выходные: воскресенье и суббота
            ];

            // Локация создается после или в момент создания бизнеса
            $businessCreatedAt = strtotime($business->created_at);
            $locationCreatedAt = $this->getDistributedTimestamp($businessCreatedAt, time(), 0, 1, 'uniform');
            $locationCreatedAtFormatted = date('Y-m-d H:i:s', $locationCreatedAt);

            $locations[] = [
                'business_id' => $businessId,
                'name' => 'Главный офис',
                'city' => fake('ru_RU')->city(),
                'street' => fake('ru_RU')->streetName(),
                'house' => fake('ru_RU')->buildingNumber(),
                'working_hours' => json_encode($workingHours),
                'created_at' => $locationCreatedAtFormatted,
                'updated_at' => $locationCreatedAtFormatted,
            ];

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine();

        // Используем транзакцию для вставки локаций
        DB::beginTransaction();
        try {
            // Оптимизируем размер пакета в зависимости от объема данных
            $chunkSize = count($locations) > 100000 ? 5000 : 1000;
            $chunks = array_chunk($locations, $chunkSize);
            foreach ($chunks as $chunk) {
                DB::table('locations')->insert($chunk);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $this->command->info('   ✓ Создано локаций: '.count($locations));

        return Location::all()->keyBy('business_id')->all();
    }

    private function createServices(array $businesses): array
    {
        $services = [];
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

        $totalServices = count($businesses) * $this->servicesPerBusiness;
        $progressBar = $this->command->getOutput()->createProgressBar($totalServices);
        $progressBar->start();

        foreach ($businesses as $businessId => $business) {
            $businessCreatedAt = strtotime($business->created_at);
            $serviceCount = 0;

            foreach (array_slice($serviceTypes, 0, $this->servicesPerBusiness) as $serviceType) {
                $serviceCreatedAt = $this->getDistributedTimestamp($businessCreatedAt, time(), $serviceCount, $this->servicesPerBusiness, 'linear');
                $serviceCreatedAtFormatted = date('Y-m-d H:i:s', $serviceCreatedAt);

                $services[] = [
                    'business_id' => $businessId,
                    'name' => $serviceType['name'],
                    'description' => fake('ru_RU')->sentence(5),
                    'duration' => $serviceType['duration'],
                    'price' => $serviceType['price'] + fake()->numberBetween(-100, 500),
                    'is_active' => true,
                    'created_at' => $serviceCreatedAtFormatted,
                    'updated_at' => $serviceCreatedAtFormatted,
                ];

                $progressBar->advance();
                $serviceCount++;
            }
        }

        $progressBar->finish();
        $this->command->newLine();

        // Используем транзакцию для вставки услуг
        DB::beginTransaction();
        try {
            // Оптимизируем размер пакета в зависимости от объема данных
            $chunkSize = count($services) > 100000 ? 2500 : 500;
            $chunks = array_chunk($services, $chunkSize);
            foreach ($chunks as $chunk) {
                DB::table('services')->insert($chunk);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $this->command->info('   ✓ Создано услуг: '.count($services));

        // Возвращаем данные из массива
        $result = [];
        $serviceId = 1;
        foreach ($services as $service) {
            $service['id'] = $serviceId++;
            $result[$service['business_id']][] = (object) $service;
        }

        return $result;
    }

    private function createMasters(array $businesses, array $users): array
    {
        $masters = [];
        $mastersData = [];
        $serviceMasterPivots = [];
        $masterLocationPivots = [];

        $workingHours = [
            'from' => '09:00',
            'to' => '18:00',
            '24_hours' => false,
            'days_off' => [0, 6], // Выходные
        ];

        $specializations = [
            'Парикмахер',
            'Стилист',
            'Мастер маникюра',
            'Массажист',
            'Косметолог',
            'Барбер',
        ];

        // Предварительно загружаем все услуги и локации для оптимизации
        $allServices = Service::all()->groupBy('business_id');
        $allLocations = Location::all()->keyBy('business_id');

        $totalMasters = count($businesses) * $this->mastersPerBusiness;
        $progressBar = $this->command->getOutput()->createProgressBar($totalMasters);
        $progressBar->start();

        foreach ($businesses as $businessId => $business) {
            $businessServices = $allServices[$businessId] ?? [];
            $businessServicesIds = $businessServices->pluck('id')->toArray();
            $businessLocation = $allLocations[$businessId] ?? null;
            $businessCreatedAt = strtotime($business->created_at);

            for ($i = 0; $i < $this->mastersPerBusiness; $i++) {
                // Распределяем создание мастеров по времени существования бизнеса
                $masterCreatedAt = $this->getDistributedTimestamp($businessCreatedAt, time(), $i, $this->mastersPerBusiness, 'linear');
                $masterCreatedAtFormatted = date('Y-m-d H:i:s', $masterCreatedAt);

                $mastersData[] = [
                    'business_id' => $businessId,
                    'user_id' => null,
                    'first_name' => fake('ru_RU')->firstName(),
                    'last_name' => fake('ru_RU')->lastName(),
                    'specialization' => fake()->randomElement($specializations),
                    'description' => fake('ru_RU')->sentence(8),
                    'email' => fake()->optional(0.5)->safeEmail(),
                    'working_hours' => json_encode($workingHours),
                    'is_active' => true,
                    'created_at' => $masterCreatedAtFormatted,
                    'updated_at' => $masterCreatedAtFormatted,
                ];

                // Генерируем связи для текущего мастера (индекс в массиве)
                $masterIndex = count($mastersData) - 1;

                // Привязываем мастера к услугам (2-5 услуг на мастера)
                $masterServicesCount = min(fake()->numberBetween(2, 5), count($businessServicesIds));
                $masterServices = fake()->randomElements($businessServicesIds, $masterServicesCount);

                foreach ($masterServices as $serviceId) {
                    // Используем временный ID, заменим после вставки
                    // Используем дату создания мастера для связей
                    $serviceMasterPivots[] = [
                        'service_id' => $serviceId,
                        'master_index' => $masterIndex,
                        'created_at' => $masterCreatedAtFormatted,
                        'updated_at' => $masterCreatedAtFormatted,
                    ];
                }

                // Привязываем мастера к локации
                if ($businessLocation) {
                    $masterLocationPivots[] = [
                        'master_index' => $masterIndex,
                        'location_id' => $businessLocation->id,
                        'created_at' => $masterCreatedAtFormatted,
                        'updated_at' => $masterCreatedAtFormatted,
                    ];
                }

                $masters[] = $masterIndex;
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->command->newLine();

        // Вставляем мастеров пакетами и получаем ID
        $masterIdMap = [];
        // Используем транзакцию для вставки мастеров
        DB::beginTransaction();
        try {
            // Оптимизируем размер пакета в зависимости от объема данных
            $chunkSize = count($mastersData) > 100000 ? 5000 : 1000;
            $chunks = array_chunk($mastersData, $chunkSize);
            $currentId = DB::table('masters')->max('id') ?? 0;

            foreach ($chunks as $chunk) {
                DB::table('masters')->insert($chunk);
                // Генерируем ID для вставленных записей
                foreach ($chunk as $record) {
                    $currentId++;
                    $masterIdMap[] = $currentId;
                }
            }

            // Обновляем ID в связях
            $serviceMasterPivotsFixed = [];
            foreach ($serviceMasterPivots as $pivot) {
                $serviceMasterPivotsFixed[] = [
                    'service_id' => $pivot['service_id'],
                    'master_id' => $masterIdMap[$pivot['master_index']],
                    'created_at' => $pivot['created_at'],
                    'updated_at' => $pivot['updated_at'],
                ];
            }

            $masterLocationPivotsFixed = [];
            foreach ($masterLocationPivots as $pivot) {
                $masterLocationPivotsFixed[] = [
                    'master_id' => $masterIdMap[$pivot['master_index']],
                    'location_id' => $pivot['location_id'],
                    'created_at' => $pivot['created_at'],
                    'updated_at' => $pivot['updated_at'],
                ];
            }

            // Вставляем связи порциями
            $serviceChunks = array_chunk($serviceMasterPivotsFixed, $chunkSize);
            foreach ($serviceChunks as $chunk) {
                DB::table('service_master')->insert($chunk);
            }

            $locationChunks = array_chunk($masterLocationPivotsFixed, $chunkSize);
            foreach ($locationChunks as $chunk) {
                DB::table('master_location')->insert($chunk);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $this->command->info('   ✓ Создано мастеров: '.count($mastersData));

        // Возвращаем данные из массива
        $result = [];
        foreach ($mastersData as $index => $master) {
            $master['id'] = $masterIdMap[$index];
            $result[$master['business_id']][] = (object) $master;
        }

        return $result;
    }

    private function createClients(array $businesses): array
    {
        $clients = [];

        $totalClients = count($businesses) * $this->clientsPerBusiness;
        $progressBar = $this->command->getOutput()->createProgressBar($totalClients);
        $progressBar->start();

        foreach ($businesses as $businessId => $business) {
            $businessCreatedAt = strtotime($business->created_at);

            for ($i = 0; $i < $this->clientsPerBusiness; $i++) {
                // Распределяем создание клиентов по времени существования бизнеса
                $clientCreatedAt = $this->getDistributedTimestamp($businessCreatedAt, time(), $i, $this->clientsPerBusiness, 'linear');
                $clientCreatedAtFormatted = date('Y-m-d H:i:s', $clientCreatedAt);

                $clients[] = [
                    'business_id' => $businessId,
                    'first_name' => fake('ru_RU')->firstName(),
                    'last_name' => fake('ru_RU')->lastName(),
                    'email' => fake()->optional(0.7)->safeEmail(),
                    'created_at' => $clientCreatedAtFormatted,
                    'updated_at' => $clientCreatedAtFormatted,
                ];

                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->command->newLine();

        // Вставляем порциями
        // Используем транзакцию для вставки клиентов
        DB::beginTransaction();
        try {
            // Оптимизируем размер пакета в зависимости от объема данных
            $chunkSize = count($clients) > 100000 ? 5000 : 1000;
            $chunks = array_chunk($clients, $chunkSize);
            foreach ($chunks as $chunk) {
                DB::table('clients')->insert($chunk);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $this->command->info('   ✓ Создано клиентов: '.count($clients));

        // Возвращаем данные из массива
        $result = [];
        $clientId = 1;
        foreach ($clients as $client) {
            $client['id'] = $clientId++;
            $result[$client['business_id']][] = (object) $client;
        }

        return $result;
    }

    private function createAppointments(array $businesses, array $clients, array $services, array $masters, array $locations): void
    {
        $now = now();
        $appointmentCount = 0;
        $startTime = time();

        $totalAppointments = count($businesses) * $this->appointmentsPerBusiness;

        // Для очень больших объемов (20+ млн) показываем предупреждение
        if ($totalAppointments > 20000000) {
            $this->command->warn("   ⚠️  Очень большой объем данных ({$totalAppointments} записей). Это может занять значительное время!");
        }

        // Для очень больших объемов данных отключаем прогресс-бар и используем счетчик
        $showProgressBar = $totalAppointments <= 1000000;

        if ($showProgressBar) {
            $progressBar = $this->command->getOutput()->createProgressBar($totalAppointments);
            $progressBar->start();
        } else {
            $this->command->info('   ⚙️  Генерация большого объема данных, используем счетчик вместо прогресс-бара');
            $lastReportedCount = 0;
            $reportStep = max(1, (int) ($totalAppointments / 20)); // Отчет каждые 5%
        }

        // Используем уже установленный временной диапазон или рассчитываем новый
        $yearsBack = $this->calculateTotalYearsBack($totalAppointments);
        // Не переопределяем глобальную переменную, чтобы не влиять на другие методы
        $startTimestamp = Carbon::today()->subYears($yearsBack)->timestamp;
        $endTimestamp = Carbon::today()->addDays(90)->timestamp;
        $todayTimestamp = Carbon::today()->timestamp;

        // Предварительно генерируем данные для оптимизации
        $workingHours = ['09', '10', '11', '12', '13', '14', '15', '16', '17'];
        $minutes = [0, 15, 30, 45];
        $pastStatuses = ['completed', 'completed', 'completed', 'cancelled', 'pending'];
        $futureStatuses = ['confirmed', 'confirmed', 'confirmed', 'pending', 'pending'];

        // Оптимизируем размер пакета в зависимости от объема данных
        $useBulkInsert = $totalAppointments > 50000;

        if ($totalAppointments <= 100000) {
            $batchSize = 1000;
        } elseif ($totalAppointments <= 1000000) {
            $batchSize = 5000;
        } elseif ($totalAppointments <= 10000000) {
            $batchSize = 10000;
        } elseif ($totalAppointments <= 50000000) {
            $batchSize = 20000;
        } else {
            // Для очень больших объемов (50+ млн) используем меньшие пакеты
            // чтобы избежать проблем с памятью
            $batchSize = 10000;
        }

        // Предварительно генерируем случайные данные для ускорения
        $randomSentences = [];
        $faker = fake('ru_RU');
        for ($i = 0; $i < 100; $i++) {
            $randomSentences[] = $faker->sentence(10);
        }

        foreach ($businesses as $businessId => $business) {
            $businessClients = $clients[$businessId] ?? [];
            $businessServices = $services[$businessId] ?? [];
            $businessMasters = $masters[$businessId] ?? [];
            $businessLocation = $locations[$businessId] ?? null;

            if (empty($businessClients) || empty($businessServices) || empty($businessMasters) || ! $businessLocation) {
                continue;
            }

            // Предварительно выбираем случайные клиенты, услуги и мастеров для этого бизнеса
            // Проверяем, является ли $businessClients коллекцией или массивом
            $clientIds = is_object($businessClients) && method_exists($businessClients, 'toArray')
                ? array_column($businessClients->toArray(), 'id')
                : array_column(array_map(function ($obj) {
                    return (array) $obj;
                }, $businessClients), 'id');

            // Проверяем, является ли $businessServices коллекцией или массивом
            $serviceModels = is_object($businessServices) && method_exists($businessServices, 'all')
                ? $businessServices->all()
                : $businessServices;

            // Проверяем, является ли $businessMasters коллекцией или массивом
            $masterModels = is_object($businessMasters) && method_exists($businessMasters, 'all')
                ? $businessMasters->all()
                : $businessMasters;

            $businessAppointments = [];

            // Предварительно генерируем даты создания для растягивания во времени
            // Учитываем время создания бизнеса для более реалистичного распределения
            $businessCreatedAt = strtotime($business->created_at);
            $createdAtStart = max($businessCreatedAt, Carbon::today()->subYears($yearsBack)->timestamp);
            $createdAtEnd = Carbon::today()->timestamp;

            // Для лучшего распределения используем неравномерное распределение
            // Это обеспечит более реалистичное распределение данных во времени
            $distributionStrategy = $this->getDistributionStrategy($totalAppointments);

            for ($i = 0; $i < $this->appointmentsPerBusiness; $i++) {
                // Растягиваем дату создания записи
                // Используем стратегию распределения для более реалистичного распределения во времени
                $createdAtTimestamp = $this->getDistributedTimestamp($createdAtStart, $createdAtEnd, $i, $this->appointmentsPerBusiness, $distributionStrategy);

                // Оптимизированная генерация даты записи с распределением
                $randomTimestamp = $this->getDistributedTimestamp($startTimestamp, $endTimestamp, $i, $this->appointmentsPerBusiness, $distributionStrategy);
                $isPast = $randomTimestamp < $todayTimestamp;

                // Форматируем дату без создания объекта Carbon
                $date = date('Y-m-d', $randomTimestamp);

                // Оптимизированный выбор случайных элементов
                $clientId = $clientIds[array_rand($clientIds)];
                $service = $serviceModels[array_rand($serviceModels)];
                $master = $masterModels[array_rand($masterModels)];

                // Оптимизированное время
                $hour = $workingHours[array_rand($workingHours)];
                $minute = $minutes[array_rand($minutes)];
                $time = $hour.':'.sprintf('%02d', $minute);

                // Оптимизированный статус
                $status = $isPast
                    ? $pastStatuses[array_rand($pastStatuses)]
                    : $futureStatuses[array_rand($futureStatuses)];

                $source = $this->sources[array_rand($this->sources)];

                // Для больших объемов используем простой токен без проверки уникальности
                if ($useBulkInsert) {
                    // Для очень больших объемов (20+ млн) используем более уникальные токены
                    if ($totalAppointments > 20000000) {
                        $token = 'app-'.$businessId.'-'.$i.'-'.uniqid('', true);
                    } else {
                        $token = 'app-'.$businessId.'-'.$i.'-'.rand(1000, 9999);
                    }
                } else {
                    $token = $this->generateAppointmentToken();
                }

                $businessAppointments[] = [
                    'business_id' => $businessId,
                    'client_id' => $clientId,
                    'service_id' => $service->id,
                    'master_id' => $master->id,
                    'location_id' => $businessLocation->id,
                    'date' => $date,
                    'time' => $time,
                    'status' => $status,
                    'source' => $source,
                    'notes' => rand(0, 100) < 30 ? fake('ru_RU')->sentence(10) : null,
                    'duration' => $service->duration,
                    'price' => $service->price,
                    'token' => $token,
                    'created_at' => date('Y-m-d H:i:s', $createdAtTimestamp),
                    'updated_at' => date('Y-m-d H:i:s', $createdAtTimestamp),
                ];

                $appointmentCount++;
                // Обновляем прогресс
                if ($showProgressBar) {
                    $progressBar->advance();
                } else {
                    // Для больших объемов используем счетчик вместо прогресс-бара
                    if ($appointmentCount % $reportStep === 0 || $appointmentCount === $totalAppointments) {
                        $percent = round(($appointmentCount / $totalAppointments) * 100);
                        $this->command->info("   ⏳  Прогресс: {$appointmentCount} из {$totalAppointments} записей ({$percent}%)");
                        $lastReportedCount = $appointmentCount;
                    }
                }

                // Проверяем время выполнения для очень больших объемов
                if ($totalAppointments > 20000000 && (time() - $startTime) > 3600) {
                    $this->command->warn('   ⚠️  Выполнение занимает более 1 часа. Продолжаем...');
                    $startTime = time(); // Сбрасываем таймер
                }

                // Вставляем очень большими пакетами для больших объемов данных
                if (count($businessAppointments) >= $batchSize) {
                    // Используем отдельную транзакцию для каждого пакета записей
                    DB::beginTransaction();
                    try {
                        if ($useBulkInsert) {
                            $this->bulkInsertAppointments($businessAppointments);
                        } else {
                            $this->insertAppointmentsWithRetry($businessAppointments);
                        }
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $this->command->warn('   ⚠️  Ошибка при вставке пакета записей: '.$e->getMessage());
                    }

                    $businessAppointments = [];

                    // Для очень больших объемов (20+ млн) очищаем память
                    if ($totalAppointments > 20000000) {
                        gc_collect_cycles(); // Принудительная очистка памяти
                    }
                }
            }

            // Вставляем оставшиеся записи для текущего бизнеса
            if (! empty($businessAppointments)) {
                // Используем отдельную транзакцию для оставшихся записей
                DB::beginTransaction();
                try {
                    if ($useBulkInsert) {
                        $this->bulkInsertAppointments($businessAppointments);
                    } else {
                        $this->insertAppointmentsWithRetry($businessAppointments);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->command->warn('   ⚠️  Ошибка при вставке оставшихся записей: '.$e->getMessage());
                }

                $businessAppointments = [];
            }
        }

        if ($showProgressBar) {
            $progressBar->finish();
            $this->command->newLine();
        } else {
            $this->command->info("   ✅  Генерация записей завершена: {$appointmentCount} записей");
        }

        $this->command->info("   ✓ Создано записей: {$appointmentCount}");
        $this->command->info("   📅 Период данных: {$yearsBack} лет назад - 90 дней вперед");
        $this->command->info('   🎯 Распределение: бизнесы, услуги, мастера и клиенты распределены по времени');
    }

    /**
     * Определяет стратегию распределения в зависимости от объема данных
     */
    private function getDistributionStrategy(int $totalAppointments): string
    {
        if ($totalAppointments <= 10000) {
            return 'uniform'; // Для небольших объемов - равномерное распределение
        } elseif ($totalAppointments <= 100000) {
            return 'linear'; // Для средних объемов - линейное распределение
        } elseif ($totalAppointments <= 1000000) {
            return 'exponential'; // Для больших объемов - экспоненциальное распределение
        } else {
            return 'logarithmic'; // Для очень больших объемов - логарифмическое распределение
        }
    }

    /**
     * Генерация временной метки с учетом стратегии распределения
     */
    private function getDistributedTimestamp(int $start, int $end, int $currentIndex, int $totalCount, string $strategy): int
    {
        // Оптимизированная версия для больших объемов данных
        $duration = $end - $start;

        // Для очень больших объемов данных используем более быстрый алгоритм
        if ($totalCount > 1000000) {
            // Упрощенное распределение для больших объемов
            switch ($strategy) {
                case 'linear':
                    $progress = $currentIndex / $totalCount;

                    return (int) ($start + ($duration * (1 - $progress)));

                case 'exponential':
                    $progress = $currentIndex / $totalCount;

                    return (int) ($start + ($duration * pow($progress, 0.5)));

                case 'logarithmic':
                    $progress = max(0.001, $currentIndex / $totalCount);

                    return (int) ($start + ($duration * (log($progress * 10) / log(10))));

                case 'uniform':
                default:
                    // Для uniform используем более быстрый алгоритм
                    // Вместо rand() используем более быстрый алгоритм для очень больших объемов
                    return $start + (int) (($end - $start) * ($currentIndex / $totalCount)) + ($currentIndex % 100);
            }
        }

        // Для меньших объемов используем оригинальный алгоритм с случайностью
        switch ($strategy) {
            case 'linear':
                // Линейное распределение - больше записей в начале периода
                $progress = $currentIndex / $totalCount;
                $timestamp = $start + ($duration * (1 - $progress));
                // Добавляем небольшой случайный фактор для разнообразия
                $timestamp += rand(-$duration * 0.05, $duration * 0.05);
                break;

            case 'exponential':
                // Экспоненциальное распределение - больше записей в конце периода
                $progress = $currentIndex / $totalCount;
                $exponentialFactor = pow($progress, 0.5); // Квадратный корень для сглаживания
                $timestamp = $start + ($duration * $exponentialFactor);
                // Добавляем небольшой случайный фактор
                $timestamp += rand(-$duration * 0.1, $duration * 0.1);
                break;

            case 'logarithmic':
                // Логарифмическое распределение - очень неравномерное, больше записей в конце
                $progress = max(0.001, $currentIndex / $totalCount); // Избегаем log(0)
                $logarithmicFactor = log($progress * 10) / log(10); // log10 для нормализации
                $timestamp = $start + ($duration * $logarithmicFactor);
                // Добавляем значительный случайный фактор
                $timestamp += rand(-$duration * 0.15, $duration * 0.15);
                break;

            case 'uniform':
            default:
                // Стандартное равномерное распределение
                $timestamp = rand($start, $end);
                break;
        }

        // Убедимся, что временная метка находится в допустимом диапазоне
        return max($start, min($end, (int) $timestamp));
    }

    /**
     * Рассчитывает общий временной диапазон для всех сущностей
     * в зависимости от общего количества создаваемых записей
     *
     * Учитывая архитектуру: 1000+ бизнесов, много салонов, 10,000+ мастеров
     * Реалистичные расчеты для масштабируемой системы онлайн-записи
     */
    private function calculateTotalYearsBack(int $totalAppointments): int
    {
        // Ускоренные временные рамки для крупных систем
        if ($totalAppointments <= 1000) {
            return 1; // До 1000 записей - 1 год
        } elseif ($totalAppointments <= 10000) {
            return 2; // До 10к записей - 2 года
        } elseif ($totalAppointments <= 100000) {
            return 3; // До 100к записей - 3 года
        } elseif ($totalAppointments <= 1000000) {
            return 4; // До 1млн записей - 4 года (ускорено для масштаба)
        } elseif ($totalAppointments <= 10000000) {
            return 5; // До 10млн записей - 5 лет (реалистично для крупной сети)
        } elseif ($totalAppointments <= 50000000) {
            return 6; // До 50млн записей - 6 лет (национальная сеть)
        } elseif ($totalAppointments <= 100000000) {
            return 8; // До 100млн записей - 8 лет (международная сеть)
        } else {
            // Для гигантских объемов (100+ млн) - максимум 10 лет
            return min(10, max(8, (int) ($totalAppointments / 20000000) + 6));
        }
    }

    /**
     * Генерация уникального токена для записи
     * Формат: abc-123-def-456
     */
    private function generateAppointmentToken(): string
    {
        static $counter = 0;
        static $lastMicrotime = 0;

        // Оптимизированная версия для больших объемов данных
        // Используем комбинацию счетчика и времени для уникальности
        $microtime = microtime(true);
        if ($microtime === $lastMicrotime) {
            $counter++;
        } else {
            $lastMicrotime = $microtime;
            $counter = 0;
        }

        // Используем более быстрый алгоритм генерации токена
        $base = base_convert(mt_rand(1000000, 9999999), 10, 36).base_convert($counter, 10, 36);
        $hash = md5($base.$microtime);

        // Форматируем в нужный формат: abc-123-def-456
        $letters1 = substr($hash, 0, 3);
        $digits1 = substr($hash, 3, 3);
        $letters2 = substr($hash, 6, 3);
        $digits2 = substr($hash, 9, 3);

        return strtolower($letters1.'-'.$digits1.'-'.$letters2.'-'.$digits2);
    }

    /**
     * Генерация простого токена для массовой вставки
     * Используется когда уникальность не критична для тестовых данных
     */
    private function generateSimpleToken(int $businessId, int $index): string
    {
        return 'app-'.$businessId.'-'.$index.'-'.rand(1000, 9999);
    }

    /**
     * Генерация уникального токена с проверкой на уникальность в базе данных
     * Оптимизированная версия с кэшированием и пакетной проверкой
     */
    private function generateUniqueAppointmentToken(): string
    {
        $attempts = 0;
        $maxAttempts = 3; // Еще больше уменьшаем количество попыток

        do {
            $token = $this->generateAppointmentToken();

            // Проверяем уникальность токена в базе данных
            // Используем более быстрый запрос с лимитом 1
            $exists = DB::table('appointments')->where('token', $token)->limit(1)->exists();

            if (! $exists) {
                return $token;
            }

            $attempts++;

            // Если после нескольких попыток не удалось сгенерировать уникальный токен,
            // добавляем дополнительную энтропию
            if ($attempts >= $maxAttempts) {
                $token = $this->generateAppointmentToken().'-'.$attempts;

                return $token;
            }
        } while ($attempts < $maxAttempts);

        return $token;
    }

    private function assignUserRoles(): void
    {
        // Оптимизированное назначение ролей через прямой SQL-запрос
        $roleId = DB::table('roles')->where('name', 'user')->value('id');

        if ($roleId) {
            $userIds = DB::table('users')->pluck('id');

            $modelHasRoles = [];
            foreach ($userIds as $userId) {
                $modelHasRoles[] = [
                    'role_id' => $roleId,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $userId,
                ];
            }

            // Вставляем пачками
            // Оптимизируем размер пакета в зависимости от объема данных
            $chunkSize = count($modelHasRoles) > 100000 ? 5000 : 1000;
            $chunks = array_chunk($modelHasRoles, $chunkSize);
            foreach ($chunks as $chunk) {
                DB::table('model_has_roles')->insert($chunk);
            }
        }
    }

    /**
     * Массовая вставка записей без проверки уникальности (для больших объемов данных)
     */
    private function bulkInsertAppointments(array $appointments): void
    {
        try {
            // Используем прямую вставку без проверки уникальности
            // Вставляем порциями, чтобы не превысить лимит placeholders MySQL
            // Оптимизируем размер пакета в зависимости от объема данных
            $chunkSize = count($appointments) > 100000 ? 5000 : 1000;
            $chunks = array_chunk($appointments, $chunkSize);
            foreach ($chunks as $chunk) {
                DB::table('appointments')->insert($chunk);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Если произошла ошибка, пробуем вставить поменьше
            $chunkSize = count($appointments) > 20000000 ? 500 : 1000;
            $chunks = array_chunk($appointments, $chunkSize);

            foreach ($chunks as $chunk) {
                try {
                    DB::table('appointments')->insert($chunk);
                } catch (\Illuminate\Database\QueryException $e) {
                    // Если и это не помогает, просто пропускаем проблемные записи
                    $this->command->warn('   ⚠️  Не удалось вставить пакет записей, пропускаем...');
                }
            }
        }
    }

    /**
     * Вставка записей с обработкой конфликтов уникальности
     * Оптимизированная версия с пакетной обработкой
     */
    private function insertAppointmentsWithRetry(array $appointments): void
    {
        $maxAttempts = 2; // Уменьшаем количество попыток
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                // Вставляем порциями, чтобы не превысить лимит placeholders MySQL
                // Оптимизируем размер пакета в зависимости от объема данных
                $chunkSize = count($appointments) > 100000 ? 5000 : 1000;
                $chunks = array_chunk($appointments, $chunkSize);
                foreach ($chunks as $chunk) {
                    DB::table('appointments')->insert($chunk);
                }

                return;
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() === '23000' && strpos($e->getMessage(), 'appointments_token_unique') !== false) {
                    // Конфликт уникальности токена, перегенерируем токены
                    $attempt++;
                    $this->command->warn('   ⚠️  Обнаружены дубликаты токенов, перегенерация...');

                    // Быстрая перегенерация токенов
                    $tokens = [];
                    foreach ($appointments as &$appointment) {
                        $tokens[] = $this->generateAppointmentToken();
                        $appointment['token'] = $tokens[count($tokens) - 1];
                    }

                    continue;
                }

                // Другие ошибки - пробрасываем дальше
                throw $e;
            }
        }

        // Если не удалось после нескольких попыток
        throw new \Exception("Не удалось вставить записи после {$maxAttempts} попыток");
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
