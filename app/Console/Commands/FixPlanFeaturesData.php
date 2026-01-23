<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PlanFeature;

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
        
        // Находим все записи с типом integer, но со значениями "false" или "true"
        $incorrectFeatures = PlanFeature::where('feature_type', 'integer')
            ->whereIn('feature_value', ['false', 'true'])
            ->get();
        
        if ($incorrectFeatures->isEmpty()) {
            $this->info('Неправильных данных не найдено.');
            return 0;
        }
        
        $this->warn("Найдено {$incorrectFeatures->count()} неправильных записей:");
        
        foreach ($incorrectFeatures as $feature) {
            $this->line("  - Plan ID: {$feature->plan_id}, Key: {$feature->feature_key}, Value: {$feature->feature_value}");
        }
        
        $this->info('Исправление записей...');
        $fixed = 0;
        foreach ($incorrectFeatures as $feature) {
            $feature->feature_value = '';
            $feature->save();
            $fixed++;
        }
        
        $this->info("Исправлено {$fixed} записей.");
        
        return 0;
    }
}
