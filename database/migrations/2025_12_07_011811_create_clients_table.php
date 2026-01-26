<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('telegram_user_id')->nullable();
            $table->timestamps();

            // Индексы для оптимизации дашборда
            $table->index('created_at'); // группировка по дате создания
            $table->index('business_id'); // подсчет клиентов по бизнесу
            
            // Индексы для поиска
            $table->index('first_name'); // поиск по имени
            $table->index('last_name'); // поиск по фамилии
        });

        // FULLTEXT индекс для быстрого текстового поиска
        DB::statement('ALTER TABLE clients ADD FULLTEXT INDEX ft_clients_name (first_name, last_name)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
