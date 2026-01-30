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
        Schema::create('masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->string('specialization');
            $table->string('email')->nullable();
            $table->json('working_hours')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->index(['business_id', 'is_active'], 'masters_business_active');
        });

        // FULLTEXT индекс для быстрого текстового поиска
        DB::statement('ALTER TABLE masters ADD FULLTEXT INDEX ft_masters_name (first_name, last_name)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masters');
    }
};
