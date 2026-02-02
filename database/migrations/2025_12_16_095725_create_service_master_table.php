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
        Schema::create('service_master', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('master_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2)->nullable()->comment('Если NULL, берем базовую цену из services');
            $table->integer('duration')->nullable()->comment('Длительность в минутах. Если NULL, берем из services');
            $table->timestamps();

            $table->unique(['service_id', 'master_id']);
            $table->index(['master_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_master');
    }
};
