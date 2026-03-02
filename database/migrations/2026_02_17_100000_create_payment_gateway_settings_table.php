<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_gateway_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gateway', 50)->unique();      // 'bepaid', 'freekassa'
            $table->boolean('enabled')->default(false);   // Включён ли шлюз
            $table->boolean('test_mode')->nullable();     // null = из .env, true/false = переопределение
            $table->unsignedSmallInteger('priority')->default(0); // Порядок отображения
            $table->json('config')->nullable();           // Дополнительные настройки
            $table->timestamps();

            $table->index('enabled');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_settings');
    }
};
