<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Проверяем, существует ли уже колонка user_id
        if (!Schema::hasColumn('subscription_usages', 'user_id')) {
            Schema::table('subscription_usages', function (Blueprint $table) {
                // Сначала добавляем user_id без foreign key
                $table->unsignedBigInteger('user_id')->nullable()->after('subscription_id');
            });
        }

        // Заполняем user_id данными из subscription->user_id (если есть NULL значения)
        DB::statement('
            UPDATE subscription_usages su
            INNER JOIN subscriptions s ON su.subscription_id = s.id
            SET su.user_id = s.user_id
            WHERE su.user_id IS NULL
        ');

        // Получаем список foreign keys
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'subscription_usages' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        $fkNames = array_column($foreignKeys, 'CONSTRAINT_NAME');

        // Сначала удаляем foreign key на subscription_id, так как он использует индекс
        if (in_array('subscription_usages_subscription_id_foreign', $fkNames)) {
            Schema::table('subscription_usages', function (Blueprint $table) {
                $table->dropForeign(['subscription_id']);
            });
        }

        Schema::table('subscription_usages', function (Blueprint $table) use ($fkNames) {
            // Удаляем foreign key для business_id, если он существует
            if (in_array('subscription_usages_business_id_foreign', $fkNames)) {
                $table->dropForeign(['business_id']);
            }
            
            // Удаляем старый уникальный индекс, если он существует
            if (Schema::hasIndex('subscription_usages', 'sub_usages_unique')) {
                $table->dropUnique('sub_usages_unique');
            }
            
            // Удаляем колонку business_id, если она существует
            if (Schema::hasColumn('subscription_usages', 'business_id')) {
                $table->dropColumn('business_id');
            }
            
            // Делаем user_id NOT NULL и добавляем foreign key
            if (Schema::hasColumn('subscription_usages', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable(false)->change();
                if (!in_array('subscription_usages_user_id_foreign', $fkNames)) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
            }
            
            // Восстанавливаем foreign key на subscription_id, если его нет
            if (!in_array('subscription_usages_subscription_id_foreign', $fkNames)) {
                $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            }
            
            // Создаем новый уникальный индекс с user_id, если его нет
            if (!Schema::hasIndex('subscription_usages', 'user_feature_period_unique')) {
                $table->unique(['user_id', 'feature_key', 'period_start'], 'user_feature_period_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_usages', function (Blueprint $table) {
            // Удаляем новый уникальный индекс
            $table->dropUnique('user_feature_period_unique');
            
            // Удаляем user_id
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            
            // Восстанавливаем business_id
            $table->foreignId('business_id')->after('subscription_id')->constrained()->cascadeOnDelete();
            
            // Восстанавливаем старый уникальный индекс
            $table->unique(['subscription_id', 'feature_key', 'period_start'], 'sub_usages_unique');
        });
    }
};
