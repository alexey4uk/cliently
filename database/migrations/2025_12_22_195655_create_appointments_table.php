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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique()->nullable();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('master_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->time('time');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->integer('duration')->nullable(); // Переопределение длительности услуги
            $table->decimal('price', 10, 2)->nullable(); // Переопределение цены услуги
            $table->timestamps();

            // Существующие индексы
            $table->index(['business_id', 'date']);
            $table->index(['master_id', 'date', 'time']);
            $table->index('token');

            // Дополнительные индексы для оптимизации дашборда
            $table->index('created_at'); // группировка по дате создания
            $table->index('date'); // фильтрация по дате записи
            $table->index('status'); // фильтрация по статусу
            $table->index(['business_id', 'created_at']); // активные бизнесы
            $table->index(['date', 'status']); // фильтрация по дате и статусу
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
