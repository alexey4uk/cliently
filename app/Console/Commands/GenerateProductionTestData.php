<?php

namespace App\Console\Commands;

use Database\Seeders\ProductionTestSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateProductionTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed:production-test 
                            {--fresh : Очистить базу перед заполнением}
                            {--y|yes : Пропустить все подтверждения}
                            {--force : Принудительный запуск без подтверждения}
                            {--users=100 : Количество пользователей}
                            {--clients=50 : Количество клиентов на бизнес}
                            {--services=8 : Количество услуг на бизнес}
                            {--masters=3 : Количество мастеров на бизнес}
                            {--appointments=100 : Количество записей на бизнес}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерация большого объема тестовых данных для симуляции продакшена';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isFresh = $this->option('fresh');
        $isForce = $this->option('force');
        $skipConfirm = $this->option('yes') || $isForce;

        // Получаем параметры количества
        $users = (int) $this->option('users');
        $clients = (int) $this->option('clients');
        $services = (int) $this->option('services');
        $masters = (int) $this->option('masters');
        $appointments = (int) $this->option('appointments');

        // Передаем параметры в переменные окружения для сидера
        putenv("SEED_USERS_COUNT={$users}");
        putenv("SEED_CLIENTS_PER_BUSINESS={$clients}");
        putenv("SEED_SERVICES_PER_BUSINESS={$services}");
        putenv("SEED_MASTERS_PER_BUSINESS={$masters}");
        putenv("SEED_APPOINTMENTS_PER_BUSINESS={$appointments}");

        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║     Генерация тестовых данных для симуляции продакшена      ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Показываем что будет создано
        $this->table(
            ['Сущность', 'Количество'],
            [
                ['Пользователи', number_format($users)],
                ['Бизнесы', number_format($users) . ' (по 1 на пользователя)'],
                ['Локации', number_format($users) . ' (по 1 на бизнес)'],
                ['Услуги', '~' . number_format($users * $services) . " (по {$services} на бизнес)"],
                ['Мастера', '~' . number_format($users * $masters) . " (по {$masters} на бизнес)"],
                ['Клиенты', '~' . number_format($users * $clients) . " (по {$clients} на бизнес)"],
                ['Записи', '~' . number_format($users * $appointments) . " (по {$appointments} на бизнес)"],
            ]
        );

        $this->newLine();
        $this->info('⏱️  Примерное время выполнения: 2-5 минут');
        $this->newLine();

        if ($isFresh) {
            $this->warn('⚠️  Будет выполнена полная очистка базы данных!');
            $this->newLine();
        }

        // Запрос подтверждения
        if (! $skipConfirm && ! $this->confirm('Продолжить?', true)) {
            $this->info('Отменено.');

            return 0;
        }

        $this->newLine();

        // Очистка базы если нужно
        if ($isFresh) {
            $this->info('🗑️  Очистка базы данных...');

            if ($skipConfirm || $this->confirm('Вы уверены? Все данные будут удалены!', false)) {
                Artisan::call('migrate:fresh', ['--force' => true]);
                $this->info('✓ База данных очищена');
                $this->newLine();

                // Запускаем базовые сидеры
                $this->info('📦 Запуск базовых сидеров...');
                Artisan::call('db:seed', [
                    '--class' => 'DatabaseSeeder',
                    '--force' => true,
                ]);
                $this->info('✓ Базовые данные созданы');
                $this->newLine();
            } else {
                $this->error('Отменено.');

                return 1;
            }
        }

        // Запуск ProductionTestSeeder
        $this->newLine();
        Artisan::call('db:seed', [
            '--class' => ProductionTestSeeder::class,
            '--force' => true,
        ], $this->output);

        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║                    ✅ Успешно завершено!                     ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        $this->info('🔑 Учетные данные для входа:');
        $this->table(
            ['Email', 'Password'],
            [
                ['user1@cliently.test', 'password'],
                ['user2@cliently.test', 'password'],
                ['user3@cliently.test', 'password'],
                ['... user100@cliently.test', 'password'],
            ]
        );

        $this->newLine();
        $this->info('💡 Совет: Используйте user1@cliently.test для быстрого входа');

        return 0;
    }
}
