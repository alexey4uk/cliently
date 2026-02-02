<?php

namespace App\Console\Commands;

use App\Models\PlanFeature;
use Illuminate\Console\Command;

class FixPlanFeaturesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plans:fix-features-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Исправляет неправильные данные в plan_features: очищает boolean значения для integer типов';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Поиск неправильных данных в plan_features...');

        // Находим записи, где метрика типа integer, но value — "true" или "false"
        $incorrectFeatures = PlanFeature::with('metric')
            ->whereHas('metric', function ($q) {
                $q->where('type', 'integer');
            })
            ->whereIn('value', ['false', 'true'])
            ->get();

        if ($incorrectFeatures->isEmpty()) {
            $this->info('Неправильных данных не найдено.');

            return 0;
        }

        $this->warn("Найдено {$incorrectFeatures->count()} неправильных записей:");

        foreach ($incorrectFeatures as $feature) {
            $key = $feature->metric ? $feature->metric->key : '?';
            $this->line("  - Plan ID: {$feature->plan_id}, Key: {$key}, Value: {$feature->value}");
        }

        $this->info('Исправление записей...');
        $fixed = 0;
        foreach ($incorrectFeatures as $feature) {
            $feature->update(['value' => '0']);
            $fixed++;
        }

        $this->info("Исправлено {$fixed} записей.");

        return 0;
    }
}
