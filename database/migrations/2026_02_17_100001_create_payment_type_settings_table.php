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
        Schema::create('payment_type_settings', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->unique();              // 'subscription', 'purchase', 'donation', 'balance'
            $table->boolean('enabled')->default(false);        // Включён ли тип оплаты
            $table->string('default_gateway', 50)->nullable(); // Шлюз по умолчанию для этого типа
            $table->json('allowed_gateways')->nullable();      // Переопределение разрешённых шлюзов из конфига
            $table->decimal('min_amount', 12, 2)->nullable();  // Минимальная сумма оплаты
            $table->decimal('max_amount', 12, 2)->nullable();  // Максимальная сумма оплаты
            $table->json('config')->nullable();                // Дополнительные настройки
            $table->timestamps();

            $table->index('enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_type_settings');
    }
};
