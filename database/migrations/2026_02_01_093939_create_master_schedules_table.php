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
        Schema::create('master_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0-6 или 1-7
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_working')->default(true);
            $table->timestamps();

            $table->unique(['master_id', 'day_of_week']); // Чтобы не было двух понедельников
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_schedules');
    }
};
