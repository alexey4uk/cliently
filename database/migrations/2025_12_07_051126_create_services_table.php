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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();

            $table->foreignId('category_id')->nullable()->constrained('service_categories')->nullOnDelete();

            $table->string('name');
            $table->text('description')->nullable();

            $table->integer('duration')->default(30)->comment('Длительность в минутах');
            $table->integer('preparation_time')->default(0)->comment('Технический перерыв после услуги');

            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Индексы
            $table->index(['business_id', 'is_active'], 'idx_services_list');
            $table->index('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
